<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

/**
 * Class CardDetails
 */
class CardDetails
{
    /**
     * @var string
     */
    protected $brand;

    /**
     * @var string
     */
    protected $lastFour;

    /**
     * @var int
     */
    protected $expirationMonth;

    /**
     * @var int
     */
    protected $expirationYear;

    /**
     * @var int
     */
    protected $nameOnCard;

    /**
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     *
     * @return CardDetails
     */
    public function setBrand(string $brand): CardDetails
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastFour(): string
    {
        return $this->lastFour;
    }

    /**
     * @param string $lastFour
     *
     * @return CardDetails
     */
    public function setLastFour(string $lastFour): CardDetails
    {
        $this->lastFour = $lastFour;

        return $this;
    }

    /**
     * @return int
     */
    public function getExpirationMonth(): int
    {
        return $this->expirationMonth;
    }

    /**
     * @param int $expirationMonth
     *
     * @return CardDetails
     */
    public function setExpirationMonth(int $expirationMonth): CardDetails
    {
        $this->expirationMonth = $expirationMonth;

        return $this;
    }

    /**
     * @return int
     */
    public function getExpirationYear(): int
    {
        return $this->expirationYear;
    }

    /**
     * @param int $expirationYear
     *
     * @return CardDetails
     */
    public function setExpirationYear(int $expirationYear): CardDetails
    {
        $this->expirationYear = $expirationYear;

        return $this;
    }

    /**
     * @return int
     */
    public function getNameOnCard(): int
    {
        return $this->nameOnCard;
    }

    /**
     * @param int $nameOnCard
     *
     * @return CardDetails
     */
    public function setNameOnCard(int $nameOnCard): CardDetails
    {
        $this->nameOnCard = $nameOnCard;

        return $this;
    }
}
