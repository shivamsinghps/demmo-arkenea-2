<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RtpDetails
 */
class RtpDetails
{
    /**
     * @var string
     *
     * @Assert\Length(min=1, max=140)
     */
    protected $destinationRemittanceData;

    /**
     * @return string
     */
    public function getDestinationRemittanceData(): string
    {
        return $this->destinationRemittanceData;
    }

    /**
     * @param string $destinationRemittanceData
     */
    public function setDestinationRemittanceData(string $destinationRemittanceData): void
    {
        $this->destinationRemittanceData = $destinationRemittanceData;
    }
}
