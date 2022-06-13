<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 17:29
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class Requirement
{
    /** @var int */
    private $id;

    /** @var string */
    private $backofficeId;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var int */
    private $sortOrder;

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
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}
