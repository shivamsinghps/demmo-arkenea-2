<?php

namespace Tests\FMT\InfrastructureBundle\Assumptions\Nebook\Traits;

use FMT\InfrastructureBundle\Service\Nebook\RestApi\Client;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Exception;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\{CartItem,
    Course,
    Department,
    Section,
    Term};

trait CartHelperTrait
{
    /** @var Client */
    private $client;

    /** @var Term */
    private $term;

    /** @var Department[] */
    private $departments = [];

    /** @var Department */
    private $department;

    /** @var Course[] */
    private $courses = [];

    /** @var Course */
    private $course;

    /** @var Section[] */
    private $sections = [];

    /** @var CartItem[] */
    private $cartItems = [];

    /**
     * @param Client $client
     * @param Term $term
     * @return string|null
     * @throws Exception
     */
    public function getIsbn(Client $client, Term $term)
    {
        if (empty($this->sections)) {
            $this->setupProperties($client, $term);
        }

        foreach ($this->sections as $section) {
            foreach ($section->getMaterials() as $material) {
                if ($material->getFamily()->getInfo()->getIsbn()) {
                    return $material->getFamily()->getInfo()->getIsbn();
                }
            }
        }

        return null;
    }

    /**
     * @param Client $client
     * @param Term $term
     * @param int $itemsNumber
     * @return CartItem[]
     * @throws Exception
     */
    public function getItems(Client $client, Term $term, int $itemsNumber)
    {
        if (!empty($this->cartItems)) {
            return $this->cartItems;
        }
        $this->setupProperties($client, $term);

        while ($this->sections) {
            $section = array_shift($this->sections);

            foreach ($section->getMaterials() as $material) {
                if (empty($material->getFamily()->getProducts())) {
                    continue;
                }

                foreach ($material->getFamily()->getProducts() as $product) {
                    if ($product->getCalculatedInventory() <= 0) {
                        continue;
                    }

                    $cartItem = new CartItem();
                    $cartItem->setRental(false);
                    $cartItem->setPrice($product->getPrice());
                    $cartItem->setFamilyId($material->getFamily()->getId());
                    $cartItem->setQuantity(1);
                    $cartItem->setSku($product->getSku());
                    $cartItem->setAllowSubstitution(true);

                    $this->cartItems[] = $cartItem;

                    if (count($this->cartItems) >= $itemsNumber) {
                        break 3;
                    }
                }
            }

            $this->nextCourse();
        }

        return $this->cartItems;
    }

    /**
     * @throws Exception
     */
    protected function getDepartments()
    {
        $this->departments = $this->client->departmentsGetActiveByTermId($this->term->getId());
        $this->nextDepartment();
    }

    protected function nextDepartment()
    {
        if (!$this->departments) {
            return;
        }

        $this->department = array_shift($this->departments);

        $this->getCourses();
    }

    protected function getCourses()
    {
        try {
            $this->courses = $this->client->coursesGetByDepartmentIdAndTermId(
                $this->department->getId(),
                $this->term->getId()
            );
        } catch (Exception $e) {
            $this->courses = [];
        }

        $this->nextCourse();
    }

    protected function nextCourse()
    {
        if (!$this->courses) {
            $this->nextDepartment();
            return;
        }

        $this->course = array_shift($this->courses);

        $this->getSections();
    }

    protected function getSections()
    {
        try {
            $this->sections = $this->client->sectionsGetByCourseId($this->course->getId());
        } catch (Exception $e) {
            $this->sections = [];
        }
    }

    /**
     * @param Client $client
     * @param Term $term
     * @throws Exception
     */
    private function setupProperties(Client $client, Term $term)
    {
        $this->client = $client;
        $this->term = $term;
        $this->getDepartments();
    }
}
