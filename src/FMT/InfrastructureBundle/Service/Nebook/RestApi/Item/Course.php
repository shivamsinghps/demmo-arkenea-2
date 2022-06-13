<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 16:11
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class Course
{
    /** @var int */
    private $id;

    /** @var int */
    private $termId;

    /** @var string */
    private $backofficeId;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var Department */
    private $department;

    /** @var Section[] */
    private $sections;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTermId()
    {
        return $this->termId;
    }

    /**
     * @return string
     */
    public function getBackofficeId()
    {
        return $this->backofficeId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @return Section[]
     */
    public function getSections()
    {
        return $this->sections;
    }
}
