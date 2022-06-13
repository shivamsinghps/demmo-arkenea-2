<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

/**
 * Class UnverifiedCustomer
 */
class UnverifiedCustomer extends AbstractCustomer
{
    /**
     * @var string|null
     */
    protected $businessName;

    /**
     * @return string|null
     */
    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    /**
     * @param string|null $businessName
     *
     * @return $this
     */
    public function setBusinessName(?string $businessName): self
    {
        $this->businessName = $businessName;

        return $this;
    }
}
