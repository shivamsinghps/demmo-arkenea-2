<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\BookstorePayment;

/**
 * Class FundingSourceOptions
 */
class FundingSourceOptions
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $routingNumber;

    /**
     * @var string
     */
    private $accountNumber;

    /**
     * @param string $name
     * @param string $routingNumber
     * @param string $accountNumber
     */
    public function __construct(string $name, string $routingNumber, string $accountNumber)
    {
        $this->name = $name;
        $this->routingNumber = $routingNumber;
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRoutingNumber(): string
    {
        return $this->routingNumber;
    }

    /**
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }
}
