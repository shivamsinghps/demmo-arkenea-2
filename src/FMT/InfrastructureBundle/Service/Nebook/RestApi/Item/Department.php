<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 16:18
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class Department
{
    /** @var int */
    private $id;

    /** @var string */
    private $backofficeId;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $abbreviation;

    /** @var Campus */
    private $campus;

    /** @var Course[] */
    private $courses;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return string
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }

    /**
     * @return Campus
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @return Course[]
     */
    public function getCourses()
    {
        return $this->courses;
    }
}
