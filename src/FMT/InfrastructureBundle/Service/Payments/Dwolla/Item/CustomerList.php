<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

/**
 * Class CustomerList
 */
class CustomerList
{
    /**
     * @var AbstractCustomer[]
     */
    protected $customers;

    /**
     * @var ListInformation|null
     */
    protected $information;

    /**
     * @param AbstractCustomer[] $customers
     */
    public function __construct(array $customers = [], ?ListInformation $information = null)
    {
        $this->customers = $customers;
        $this->information = $information;
    }

    /**
     * @return AbstractCustomer[]
     */
    public function getCustomers(): array
    {
        return $this->customers;
    }

    /**
     * @return ListInformation|null
     */
    public function getInformation(): ?ListInformation
    {
        return $this->information;
    }

    /**
     * @param ListInformation|null $information
     */
    public function setInformation(?ListInformation $information): void
    {
        $this->information = $information;
    }
}
