<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 16:53
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class Section
{
    /** @var int */
    private $id;

    /** @var string */
    private $backofficeId;

    /** @var string */
    private $name;

    /** @var string */
    private $registrationNumber;

    /** @var string */
    private $instructorEmail;

    /** @var  string */
    private $instructorName;

    /** @var  int */
    private $estimatedEnrollment;

    /** @var Course */
    private $course;

    /** @var Material[] */
    private $materials;

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
    public function getRegistrationNumber()
    {
        return $this->registrationNumber;
    }

    /**
     * @return string
     */
    public function getInstructorEmail()
    {
        return $this->instructorEmail;
    }

    /**
     * @return string
     */
    public function getInstructorName()
    {
        return $this->instructorName;
    }

    /**
     * @return int
     */
    public function getEstimatedEnrollment()
    {
        return $this->estimatedEnrollment;
    }

    /**
     * @return Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @return Material[]
     */
    public function getMaterials()
    {
        return $this->materials;
    }
}
