<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 11:37
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

/**
 * Class Term
 * @package FMT\InfrastructureBundle\Service\Nebook\RestApi\Item
 */
class Term
{
    const STATUS_OPEN = 1;
    const STATUS_HISTORICAL = 2;
    const STATUS_CLOSED = 3;

    const ADOPTION_STATUS_OPEN = 1;
    const ADOPTION_STATUS_VIEW_ONLY = 2;
    const ADOPTION_STATUS_CLOSED = 3;

    /** @var int */
    private $id;

    /** @var string */
    private $backofficeId;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var Campus */
    private $campus;

    /** @var \DateTime */
    private $preorderCreateStartDate;

    /** @var \DateTime */
    private $preorderCreateEndDate;

    /** @var \DateTime */
    private $preorderGenerateDate;

    /** @var \DateTime */
    private $preorderStopEditDate;

    /** @var \DateTime */
    private $reservationEndDate;

    /** @var \DateTime */
    private $reservationPickupEndDate;

    /** @var int */
    private $sortOrder;

    /** @var int */
    private $adoptionStatus;

    /** @var int */
    private $status;

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
     * @return Campus
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @return \DateTime
     */
    public function getPreorderCreateStartDate()
    {
        return $this->preorderCreateStartDate;
    }

    /**
     * @return \DateTime
     */
    public function getPreorderCreateEndDate()
    {
        return $this->preorderCreateEndDate;
    }

    /**
     * @return \DateTime
     */
    public function getPreorderGenerateDate()
    {
        return $this->preorderGenerateDate;
    }

    /**
     * @return \DateTime
     */
    public function getPreorderStopEditDate()
    {
        return $this->preorderStopEditDate;
    }

    /**
     * @return \DateTime
     */
    public function getReservationEndDate()
    {
        return $this->reservationEndDate;
    }

    /**
     * @return \DateTime
     */
    public function getReservationPickupEndDate()
    {
        return $this->reservationPickupEndDate;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @return int
     */
    public function getAdoptionStatus()
    {
        return $this->adoptionStatus;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}
