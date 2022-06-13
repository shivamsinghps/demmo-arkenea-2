<?php

namespace FMT\PublicBundle\Controller\Common;

use FMT\DataBundle\Entity\UserMajor;
use FMT\DomainBundle\Service\BookManagerInterface;
use FMT\DomainBundle\Service\Manager\BookManager;
use FMT\PublicBundle\Controller\AbstractBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BookController
 *
 * @var Response
 * @var Route
 * @var Template
 * @var Security
 * @var Method
 * @var ParamConverter
 *
 * @package FMT\PublicBundle\Controller
 * @Route("/book")
 * @Template()
 */
class BookController extends AbstractBaseController
{
    const ROUTE_LIST = "fmt-common-book-list";
    const ROUTE_COURSES = "fmt-common-book-courses";
    const ROUTE_SEARCH = "fmt-common-book-search";

    /** @var BookManagerInterface */
    protected $manager;

    /**
     * @required
     * @param BookManagerInterface $manager
     * @return $this
     */
    public function setManager(BookManagerInterface $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @param Request $request
     * @param UserMajor $major
     * @return JsonResponse
     * @Route("/courses/{id}", name=BookController::ROUTE_COURSES)
     */
    public function coursesAction(Request $request, UserMajor $major)
    {
        $this->checkAjaxRequest($request);

        $nextSection = BookManager::getNextSectionType(BookManager::SECTION_COURSES);
        $list = $this->manager->getByType(BookManager::SECTION_COURSES, $major);

        return $this->getSuccessAjaxResponse($list, $nextSection, $major);
    }

    /**
     * @param Request $request
     * @param UserMajor $major
     * @param string $type
     * @param mixed $sectionId
     * @return JsonResponse
     * @Route("/list/{id}/{type}/{sectionId}", name=BookController::ROUTE_LIST)
     */
    public function listAction(Request $request, UserMajor $major, $type, $sectionId)
    {
        $this->checkAjaxRequest($request);

        $nextSection = BookManager::getNextSectionType($type);
        $list = $this->manager->getByType($type, $sectionId);

        return $this->getSuccessAjaxResponse($list, $nextSection, $major);
    }

    /**
     * @param Request $request
     * @param UserMajor $major
     * @return JsonResponse
     * @Route("/search/isbn/{id}", defaults={"id": "0"}, name=BookController::ROUTE_SEARCH)
     */
    public function searchAction(Request $request, UserMajor $major = null)
    {
        $this->checkAjaxRequest($request);
        $list = $this->manager->getProductsByIsbn($request->get('isbn'), $major);

        foreach ($list as &$item) {
            $item = $this->normalizeObjectForAjax($item);
            $item['label'] = $this->renderView('@Public/common/book/_isbn_search_item.html.twig', [
                'product' => $item
            ]);
        }
        
        return $this->createSuccessAjaxResponse($list);
    }

    /**
     * @param $item
     * @param $nextSection
     * @param UserMajor $major
     */
    protected function addUrl(&$item, $nextSection, UserMajor $major)
    {
        if (!empty($item['id'])) {
            $item['url'] = $this->generateUrl(BookController::ROUTE_LIST, [
                'id' => $major->getId(),
                'sectionId' => $item['id'],
                'type' => $nextSection,
            ]);
        }
    }

    /**
     * @param array $list
     * @param string|null $nextSection
     * @param UserMajor $major
     * @return JsonResponse
     */
    protected function getSuccessAjaxResponse(array $list, ?string $nextSection, UserMajor $major): JsonResponse
    {
        $list = $this->normalizeObjectsForAjax($list);

        foreach ($list as &$item) {
            $this->addUrl($item, $nextSection, $major);
        }

        return $this->createSuccessAjaxResponse([
            'title' => $this->translate('fmt.campaign.textbooks.popup.browse.title'),
            'message' => $this->renderView(
                '@Public/components/campaign/searchBookPopup.html.twig',
                [
                    'list' => $list,
                    'major' => [
                        'id' => $major->getId(),
                        'name' => $major->getName(),
                        'url' => $this->generateUrl(self::ROUTE_COURSES, ['id' => $major->getId()]),
                    ],
                ]
            ),
        ]);
    }
}
