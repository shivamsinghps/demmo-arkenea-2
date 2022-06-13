<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use DateTime;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\CorrelationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Transfer
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Transfer
{
    use CorrelationTrait;

    public const PROCESSING_CHANGE_DESTINATION = 'destination';
    public const PROCESSING_CHANGE_REAL_TIME_PAYMENTS = 'real-time-payments';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_PENDING = 'pending';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED = 'failed';

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
    protected $status;

    /**
     * @var DateTime|null
     */
    protected $created;

    /**
     * @var string|null
     */
    protected $individualAchId;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $destination;

    /**
     * @var Amount
     *
     * @Assert\Valid
     */
    protected $amount;

    /**
     * @var string[]|null
     *
     * @Assert\Count(min=1, max=10)
     * @Assert\All({
     *     @Assert\Length(min=1, max=254),
     * })
     */
    protected $metadata;

    /**
     * @var Fee[]|null
     *
     * @Assert\All({
     *     @Assert\Valid
     * })
     */
    protected $fees;

    /**
     * @var Clearing|null
     *
     * @Assert\Valid
     */
    protected $clearing;

    /**
     * @var AchDetails|null
     *
     * @Assert\Valid
     */
    protected $achDetails;

    /**
     * @var RtpDetails|null
     *
     * @Assert\Valid
     */
    protected $rtpDetails;

    /**
     * @var array|null
     *
     * @Assert\All({
     *     @Assert\Choice({self::PROCESSING_CHANGE_DESTINATION, self::PROCESSING_CHANGE_REAL_TIME_PAYMENTS}),
     * })
     */
    protected $processingChannel;

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
     * @return Transfer
     */
    public function setId(?string $id): Transfer
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
     * @return Transfer
     */
    public function setIri(?string $iri): Transfer
    {
        $this->iri = $iri;

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
     * @return Transfer
     */
    public function setStatus(?string $status): Transfer
    {
        $this->status = $status;

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
     * @return Transfer
     */
    public function setCreated(?DateTime $created): Transfer
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIndividualAchId(): ?string
    {
        return $this->individualAchId;
    }

    /**
     * @param string|null $individualAchId
     *
     * @return Transfer
     */
    public function setIndividualAchId(?string $individualAchId): Transfer
    {
        $this->individualAchId = $individualAchId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCorrelationId(): ?string
    {
        return $this->correlationId;
    }

    /**
     * @param string|null $correlationId
     *
     * @return Transfer
     */
    public function setCorrelationId(?string $correlationId): Transfer
    {
        $this->correlationId = $correlationId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     *
     * @return Transfer
     */
    public function setSource(string $source): Transfer
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     *
     * @return Transfer
     */
    public function setDestination(string $destination): Transfer
    {
        $this->destination = $destination;

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
     * @return Transfer
     */
    public function setAmount(Amount $amount): Transfer
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    /**
     * @param string[]|null $metadata
     *
     * @return Transfer
     */
    public function setMetadata(?array $metadata): Transfer
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @return Fee[]|null
     */
    public function getFees(): ?array
    {
        return $this->fees;
    }

    /**
     * @param Fee[]|null $fees
     *
     * @return Transfer
     */
    public function setFees(?array $fees): Transfer
    {
        $this->fees = $fees;

        return $this;
    }

    /**
     * @return Clearing|null
     */
    public function getClearing(): ?Clearing
    {
        return $this->clearing;
    }

    /**
     * @param Clearing|null $clearing
     *
     * @return Transfer
     */
    public function setClearing(?Clearing $clearing): Transfer
    {
        $this->clearing = $clearing;

        return $this;
    }

    /**
     * @return AchDetails|null
     */
    public function getAchDetails(): ?AchDetails
    {
        return $this->achDetails;
    }

    /**
     * @param AchDetails|null $achDetails
     *
     * @return Transfer
     */
    public function setAchDetails(?AchDetails $achDetails): Transfer
    {
        $this->achDetails = $achDetails;

        return $this;
    }

    /**
     * @return RtpDetails|null
     */
    public function getRtpDetails(): ?RtpDetails
    {
        return $this->rtpDetails;
    }

    /**
     * @param RtpDetails|null $rtpDetails
     *
     * @return Transfer
     */
    public function setRtpDetails(?RtpDetails $rtpDetails): Transfer
    {
        $this->rtpDetails = $rtpDetails;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getProcessingChannel(): ?array
    {
        return $this->processingChannel;
    }

    /**
     * @param array|null $processingChannel
     *
     * @return Transfer
     */
    public function setProcessingChannel(?array $processingChannel): Transfer
    {
        $this->processingChannel = $processingChannel;

        return $this;
    }
}
