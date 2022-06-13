<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Fee
 */
class Fee
{
    /**
     * @var string
     */
    protected $chargeTo;

    /**
     * @var Amount
     *
     * @Assert\Valid
     */
    protected $amount;

    /**
     * @return string
     */
    public function getChargeTo(): string
    {
        return $this->chargeTo;
    }

    /**
     * @param string $chargeTo
     *
     * @return Fee
     */
    public function setChargeTo(string $chargeTo): Fee
    {
        $this->chargeTo = $chargeTo;

        return $this;
    }

    /**
     * @return Amount
     */
    public function getAmount(): Amount
    {
        return $this->amount;
    }

    /**
     * @param Amount $amount
     *
     * @return Fee
     */
    public function setAmount(Amount $amount): Fee
    {
        $this->amount = $amount;

        return $this;
    }
}
