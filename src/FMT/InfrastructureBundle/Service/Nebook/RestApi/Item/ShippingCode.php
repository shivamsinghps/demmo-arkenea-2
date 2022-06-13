<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 12:46
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class ShippingCode
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $backofficeId;

    /** @var int */
    private $sortOrder;

    /** @var int */
    private $calculationType;

    /** @var array */
    private $details;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getBackofficeId(): string
    {
        return $this->backofficeId;
    }

    /**
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @return int
     */
    public function getCalculationType(): int
    {
        return $this->calculationType;
    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }
}
