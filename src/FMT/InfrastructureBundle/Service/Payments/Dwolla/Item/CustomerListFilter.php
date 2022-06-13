<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

/**
 * Class CustomersFilter
 */
class CustomerListFilter
{
    /**
     * @var string|null
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string|null
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $status;

    /**
     * @return array|null
     */
    public function getSearch(): ?array
    {
        $result = [];

        if (!is_null($this->firstName)) {
            $result['firstName'] = $this->firstName;
        }

        if (!is_null($this->lastName)) {
            $result['lastName'] = $this->lastName;
        }

        return !empty($result) ? $result : null;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     *
     * @return CustomerListFilter
     */
    public function setFirstName(?string $firstName): CustomerListFilter
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return CustomerListFilter
     */
    public function setLastName(string $lastName): CustomerListFilter
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return CustomerListFilter
     */
    public function setEmail(?string $email): CustomerListFilter
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     *
     * @return CustomerListFilter
     */
    public function setStatus(?string $status): CustomerListFilter
    {
        $this->status = $status;

        return $this;
    }
}
