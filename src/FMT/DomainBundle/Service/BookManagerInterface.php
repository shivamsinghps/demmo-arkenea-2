<?php

namespace FMT\DomainBundle\Service;

use FMT\DataBundle\Entity\CampaignBook;
use FMT\DataBundle\Entity\UserMajor;
use FMT\DataBundle\Entity\Order;
use FMT\DomainBundle\Type\Campaign\Book\Course;
use FMT\DomainBundle\Type\Campaign\Book\Product;
use FMT\DomainBundle\Type\Campaign\Book\Section;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Term as NebookTerm;

interface BookManagerInterface
{
    const SECTION_CAMPUSES = 'campuses';
    const SECTION_TERMS = 'terms';
    const SECTION_DEPARTMENTS = 'departments';
    const SECTION_COURSES = 'courses';
    const SECTION_SECTIONS = 'sections';
    const SECTION_PRODUCTS = "products";

    /**
     * @param CampaignBook $book
     * @throws \Exception
     */
    public function update(CampaignBook $book);

    /**
     * @param $type
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getByType($type, $id);

    /**
     * @param UserMajor $major
     * @return NebookTerm|null
     * @throws \Exception
     */
    public function getTerm(UserMajor $major);

    /**
     * @param UserMajor $major
     * @return Course[]
     * @throws \Exception
     */
    public function getCourses(UserMajor $major);

    /**
     * @param $id
     * @return Section[]
     * @throws \Exception
     */
    public function getSections($id);

    /**
     * @param $id
     * @return Product[]
     * @throws \Exception
     */
    public function getProducts($id);

    /**
     * @param $isbn
     * @param UserMajor $major
     * @return Product[]
     * @throws \Exception
     */
    public function getProductsByIsbn($isbn, UserMajor $major);

    /**
     * @param Order $order
     * @throws \Exception
     * @return string
     */
    public function pushOrder(Order $order);
}
