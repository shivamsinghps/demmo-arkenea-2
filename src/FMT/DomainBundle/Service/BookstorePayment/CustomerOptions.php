<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\BookstorePayment;

/**
 * Class CustomerOptions
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class CustomerOptions
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $businessName;

    /**
     * @var string|null
     */
    private $ipAddress;

    /**
     * @var string|null
     */
    private $correlationId;

    /**
     * @param string      $firstName
     * @param string      $lastName
     * @param string      $email
     * @param string|null $businessName
     * @param string|null $ipAddress
     * @param string|null $correlationId
     */
    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        ?string $businessName,
        ?string $ipAddress,
        ?string $correlationId
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->businessName = $businessName;
        $this->ipAddress = $ipAddress;
        $this->correlationId = $correlationId;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    /**
     * @return string|null
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * @return string|null
     */
    public function getCorrelationId(): ?string
    {
        return $this->correlationId;
    }
}
