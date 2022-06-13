<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\AddressTrait;

/**
 * Class Address
 */
class Address
{
    use AddressTrait;

    /**
     * @var string|null
     */
    protected $address3;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $stateProvinceRegion;

    /**
     * @return string|null
     */
    public function getAddress3(): ?string
    {
        return $this->address3;
    }

    /**
     * @param string|null $address3
     *
     * @return Address
     */
    public function setAddress3(?string $address3): self
    {
        $this->address3 = $address3;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return $this
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getStateProvinceRegion(): string
    {
        return $this->stateProvinceRegion;
    }

    /**
     * @param string $stateProvinceRegion
     *
     * @return $this
     */
    public function setStateProvinceRegion(string $stateProvinceRegion): self
    {
        $this->stateProvinceRegion = $stateProvinceRegion;

        return $this;
    }
}
