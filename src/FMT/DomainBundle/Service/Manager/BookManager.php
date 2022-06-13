<?php

namespace FMT\DomainBundle\Service\Manager;

use FMT\DataBundle\Entity\CampaignBook;
use FMT\DataBundle\Entity\UserMajor;
use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Mapper\CampaignBookMapper;
use FMT\DomainBundle\Event\BookEvent;
use FMT\DomainBundle\Service\BookManagerInterface;
use FMT\DomainBundle\Service\Mapper\Campaign\Book\CourseMapper;
use FMT\DomainBundle\Service\Mapper\Campaign\Book\FamilyMapper;
use FMT\DomainBundle\Service\Mapper\Campaign\Book\SectionMapper;
use FMT\DomainBundle\Service\Mapper\Order\OrderMapper;
use FMT\DomainBundle\Type\Cache\Settings;
use FMT\DomainBundle\Type\Campaign\Book\Course;
use FMT\DomainBundle\Type\Campaign\Book\Product;
use FMT\DomainBundle\Type\Campaign\Book\Section;
use FMT\InfrastructureBundle\Helper\CacheHelper;
use FMT\InfrastructureBundle\Helper\LogHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Client as ClientRest;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Client as ClientSoap;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Material as NebookMaterial;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ProductFamily as NebookFamily;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Term as NebookTerm;

/**
 * Class BookManager
 * @package FMT\DomainBundle\Service\Manager
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BookManager extends EventBasedManager implements BookManagerInterface
{
    public static $nextSection = [
        self::SECTION_CAMPUSES => self::SECTION_TERMS,
        self::SECTION_TERMS => self::SECTION_DEPARTMENTS,
        self::SECTION_DEPARTMENTS => self::SECTION_COURSES,
        self::SECTION_COURSES => self::SECTION_SECTIONS,
        self::SECTION_SECTIONS => self::SECTION_PRODUCTS,
    ];

    /** @var ClientRest */
    private $clientRest;

    /** @var ClientSoap */
    private $clientSoap;

    /** @var int */
    private $cacheTimeout;

    public function __construct(ClientRest $clientRest, ClientSoap $clientSoap, Settings $cacheSettings)
    {
        $this->clientRest = $clientRest;
        $this->clientSoap = $clientSoap;
        $this->cacheTimeout = $cacheSettings->nebookCatalogTimeout;
    }

    /**
     * @param CampaignBook $book
     * @throws \Exception
     * @return bool
     */
    public function update(CampaignBook $book)
    {
        try {
            $isUpdated = false;

            $familyId = $book->getProductFamilyId();
            $key = sprintf('family_%s', $familyId);
            $callable = function () use ($familyId) {
                return $this->clientRest->productFamilyGetById($familyId);
            };

            /** @var NebookFamily $productFamily */
            $productFamily = CacheHelper::cache($key, $callable, $this->cacheTimeout);

            foreach ($productFamily->getProducts() as $product) {
                if ($product->getSku() == $book->getSku()) {
                    CampaignBookMapper::map($book, $productFamily, $product);
                    $isUpdated = true;
                    break;
                }
            }

            if (!$isUpdated) {
                $statuses = array_keys(array_filter([
                    CampaignBook::STATUS_ORDERED => $book->getStatus() == CampaignBook::STATUS_ORDERED,
                    CampaignBook::STATUS_OUT_OF_STOCK => true,
                ]));

                $book->setStatus(array_shift($statuses));
            }

            return true;
        } catch (\Exception $exception) {
            $event = new BookEvent($book);
            $this->dispatch(BookEvent::BOOK_FAILED, $event);
            throw $exception;
        }
    }

    /**
     * @param string $type
     * @return string|null
     */
    public static function getNextSectionType($type)
    {
        return self::$nextSection[$type] ?? null;
    }

    /**
     * @param string $type
     * @return string|null
     */
    public static function getPreviousSectionType($type)
    {
        foreach (self::$nextSection as $previous => $current) {
            if ($type == $current) {
                return $previous;
            }
        }

        return null;
    }

    /**
     * @param $type
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getByType($type, $id)
    {
        $method = sprintf('get%s', ucfirst($type));

        if (!method_exists($this, $method)) {
            throw new \Exception(sprintf('Not allowed type: %s', $type));
        }

        return $this->$method($id);
    }

    /**
     * @param UserMajor $major
     * @return NebookTerm|null
     * @throws \Exception
     */
    public function getTerm(UserMajor $major)
    {
        $callable = function () {
            return $this->clientRest->termsGetOpened();
        };

        /** @var NebookTerm[] $terms */
        $terms = CacheHelper::cache('terms', $callable, $this->cacheTimeout);

        foreach ($terms as $term) {
            if ($term->getCampus()->getId() == $major->getCampusId()) {
                return $term;
            }
        }

        LogHelper::warn(sprintf(
            'Term not found for Campus (id = %s), UserMajor (id = %s)',
            $major->getCampusId(),
            $major->getId()
        ));

        return null;
    }

    /**
     * @param UserMajor $major
     * @return Course[]
     * @throws \Exception
     */
    public function getCourses(UserMajor $major)
    {
        if (!$term = $this->getTerm($major)) {
            return [];
        }

        $departmentId = $major->getDepartmentId();
        $key = sprintf('courses_by_department_%s_term_%s', $departmentId, $term->getId());
        $callable = function () use ($departmentId, $term) {
            $list = $this->clientRest->coursesGetByDepartmentIdAndTermId($departmentId, $term->getId());
            return array_filter($list, function ($item) {
                return $item->getSections();
            });
        };

        $list = CacheHelper::cache($key, $callable, $this->cacheTimeout);
        foreach ($list as &$item) {
            $item = CourseMapper::map($item);
        }

        return $list;
    }

    /**
     * @param $id
     * @return Section[]
     * @throws \Exception
     */
    public function getSections($id)
    {
        $key = sprintf('sections_by_course_%s', $id);
        $callable = function () use ($id) {
            $list = $this->clientRest->sectionsGetByCourseId($id);
            return array_filter($list, function ($item) {
                return $item->getMaterials();
            });
        };

        $list = CacheHelper::cache($key, $callable, $this->cacheTimeout);
        foreach ($list as &$item) {
            $item = SectionMapper::map($item);
        }

        return $list;
    }

    /**
     * @param $id
     * @return Product[]
     * @throws \Exception
     */
    public function getProducts($id)
    {
        $result = [];
        $key = sprintf('materials_by_section_%s', $id);
        $callable = function () use ($id) {
            return $this->clientRest->courseMaterialsGetBySectionId($id);
        };

        /** @var NebookMaterial[] $materials */
        $materials = CacheHelper::cache($key, $callable, $this->cacheTimeout);

        foreach ($materials as $material) {
            $family = $material->getFamily();
            $result = array_merge($result, FamilyMapper::map($family));
        }

        return $result;
    }

    /**
     * @param $isbn
     * @param UserMajor $major
     * @return Product[]
     * @throws \Exception
     */
    public function getProductsByIsbn($isbn, UserMajor $major)
    {
        if (!$term = $this->getTerm($major)) {
            return [];
        }

        $key = sprintf('family_by_isbn_%s', $isbn);
        $callable = function () use ($isbn, $term) {
            return $this->clientRest->productFamilyGetByIsbn($isbn, $term->getId());
        };

        $family = CacheHelper::cache($key, $callable, $this->cacheTimeout);

        $result = FamilyMapper::map($family);

        return $result;
    }

    /**
     * @param Order $order
     * @throws \Exception
     * @return string
     */
    public function pushOrder(Order $order)
    {
        $result = null;

        $nebookOrder = OrderMapper::map($order);
        $pushResponse = $this->clientSoap->pushOrder($nebookOrder);

        if (array_key_exists('PushOrderResult', $pushResponse)) {
            $result = $pushResponse['PushOrderResult']->getId();
        }

        return $result;
    }
}
