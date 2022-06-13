<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Amount
 */
class Amount
{
    public const CURRENCY_USD = 'USD';

    /**
     * @var int
     *
     * @Assert\Range(min=1)
     */
    protected $value;

    /**
     * @var string
     *
     * @Assert\Choice({self::CURRENCY_USD})
     */
    protected $currency;

    /**
     * @param int    $value
     * @param string $currency
     */
    public function __construct(int $value, string $currency)
    {
        $this->value = $value;
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return $this
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }
}
