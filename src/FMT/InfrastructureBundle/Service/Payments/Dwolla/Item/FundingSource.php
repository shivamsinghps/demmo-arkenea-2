<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FundingSource
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class FundingSource
{
    public const BANK_ACCOUNT_TYPE_CHECKING = 'checking';
    public const BANK_ACCOUNT_TYPE_SAVINGS = 'savings';
    public const BANK_ACCOUNT_TYPE_GENERAL_LEDGER = 'general-ledger';
    public const BANK_ACCOUNT_TYPE_LOAN = 'loan';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_UNVERIFIED = 'unverified';
    public const TYPE_BANK = 'bank';
    public const TYPE_BALANCE = 'balance';
    public const TYPE_CARD = 'card';

    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $iri;

    /**
     * @var string|null
     */
    protected $onDemandAuthorization;

    /**
     * @var string
     */
    protected $routingNumber;

    /**
     * @var string
     *
     * @Assert\Regex('/\d+/')
     * @Assert\Length(min=4, max=17)
     */
    protected $accountNumber;

    /**
     * @var string
     *
     * @Assert\Choice({
     *     self::BANK_ACCOUNT_TYPE_CHECKING,
     *     self::BANK_ACCOUNT_TYPE_SAVINGS,
     *     self::BANK_ACCOUNT_TYPE_GENERAL_LEDGER,
     *     self::BANK_ACCOUNT_TYPE_LOAN,
     * })
     */
    protected $bankAccountType;

    /**
     * @var string
     *
     * @Assert\Length(min=1, max=50)
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $plaidToken;

    /**
     * @var string[]|null
     */
    protected $channels;

    /**
     * @var string|null
     *
     * @Assert\Choice({self::STATUS_VERIFIED, self::STATUS_UNVERIFIED})
     */
    protected $status;

    /**
     * @var string|null
     *
     * @Assert\Choice({self::TYPE_BANK, self::TYPE_BALANCE, self::TYPE_CARD})
     */
    protected $type;

    /**
     * @var DateTime|null
     */
    protected $created;

    /**
     * @var Amount|null
     */
    protected $balance;

    /**
     * @var bool|null
     */
    protected $removed;

    /**
     * @var string|null
     */
    protected $bankName;

    /**
     * @var array|null
     */
    protected $iavAccountHolders;

    /**
     * @var string|null
     */
    protected $fingerprint;

    /**
     * @var array|null
     */
    protected $cardDetails;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     *
     * @return FundingSource
     */
    public function setId(?string $id): FundingSource
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIri(): ?string
    {
        return $this->iri;
    }

    /**
     * @param string|null $iri
     *
     * @return FundingSource
     */
    public function setIri(?string $iri): FundingSource
    {
        $this->iri = $iri;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOnDemandAuthorization(): ?string
    {
        return $this->onDemandAuthorization;
    }

    /**
     * @param string|null $onDemandAuthorization
     *
     * @return $this
     */
    public function setOnDemandAuthorization(?string $onDemandAuthorization): self
    {
        $this->onDemandAuthorization = $onDemandAuthorization;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoutingNumber(): string
    {
        return $this->routingNumber;
    }

    /**
     * @param string $routingNumber
     *
     * @return $this
     */
    public function setRoutingNumber(string $routingNumber): self
    {
        $this->routingNumber = $routingNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     *
     * @return $this
     */
    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankAccountType(): string
    {
        return $this->bankAccountType;
    }

    /**
     * @param string $bankAccountType
     *
     * @return $this
     */
    public function setBankAccountType(string $bankAccountType): self
    {
        $this->bankAccountType = $bankAccountType;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlaidToken(): ?string
    {
        return $this->plaidToken;
    }

    /**
     * @param string|null $plaidToken
     *
     * @return $this
     */
    public function setPlaidToken(?string $plaidToken): self
    {
        $this->plaidToken = $plaidToken;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getChannels(): ?array
    {
        return $this->channels;
    }

    /**
     * @param array|null $channels
     *
     * @return $this
     */
    public function setChannels(?array $channels): self
    {
        $this->channels = $channels;

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
     * @return FundingSource
     */
    public function setStatus(?string $status): FundingSource
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return FundingSource
     */
    public function setType(?string $type): FundingSource
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime|null $created
     *
     * @return FundingSource
     */
    public function setCreated(?DateTime $created): FundingSource
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return Amount|null
     */
    public function getBalance(): ?Amount
    {
        return $this->balance;
    }

    /**
     * @param Amount|null $balance
     *
     * @return FundingSource
     */
    public function setBalance(?Amount $balance): FundingSource
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getRemoved(): ?bool
    {
        return $this->removed;
    }

    /**
     * @param bool|null $removed
     *
     * @return FundingSource
     */
    public function setRemoved(?bool $removed): FundingSource
    {
        $this->removed = $removed;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    /**
     * @param string|null $bankName
     *
     * @return FundingSource
     */
    public function setBankName(?string $bankName): FundingSource
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getIavAccountHolders(): ?array
    {
        return $this->iavAccountHolders;
    }

    /**
     * @param array|null $iavAccountHolders
     *
     * @return FundingSource
     */
    public function setIavAccountHolders(?array $iavAccountHolders): FundingSource
    {
        $this->iavAccountHolders = $iavAccountHolders;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    /**
     * @param string|null $fingerprint
     *
     * @return FundingSource
     */
    public function setFingerprint(?string $fingerprint): FundingSource
    {
        $this->fingerprint = $fingerprint;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getCardDetails(): ?array
    {
        return $this->cardDetails;
    }

    /**
     * @param array|null $cardDetails
     *
     * @return FundingSource
     */
    public function setCardDetails(?array $cardDetails): FundingSource
    {
        $this->cardDetails = $cardDetails;

        return $this;
    }
}
