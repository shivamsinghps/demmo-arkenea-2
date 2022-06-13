<?php

namespace FMT\InfrastructureBundle\Service\Payments\Stripe;

use Stripe\ApiResource;
use Stripe\Collection;
use Stripe\Customer;
use Stripe\StripeObject;

/**
 * Trait CustomerTrait
 * @package FMT\InfrastructureBundle\Service\Payments\Stripe
 */
trait CustomerTrait
{
    /**
     * @param array $params
     * @return Customer|ApiResource
     */
    public function createCustomer(array $params): Customer
    {
        return Customer::create($params);
    }

    /**
     * @param string $id
     * @return Customer|StripeObject
     */
    public function getCustomer(string $id): Customer
    {
        return Customer::retrieve($id);
    }

    /**
     * @param Customer $customer
     * @param array $params
     * @return Customer
     */
    public function updateCustomer(Customer $customer, array $params): Customer
    {
        return $this->updateResource($customer, $params);
    }

    /**
     * @param array $params
     * @return Collection
     */
    public function getAllCustomers(array $params = [])
    {
        return Customer::all($params);
    }

    /**
     * @param Customer $customer
     * @return Customer|ApiResource
     */
    public function deleteCustomer(Customer $customer)
    {
        return $customer->delete();
    }
}
