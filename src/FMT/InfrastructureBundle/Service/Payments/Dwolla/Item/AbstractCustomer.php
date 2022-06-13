<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\CorrelationTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\NameTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AbstractCustomer
 */
abstract class AbstractCustomer
{
    use NameTrait;
    use CorrelationTrait;

    public const TYPE_RECEIVE_ONLY = 'receive-only';
    public const TYPE_PERSONAL     = 'personal';
    public const TYPE_BUSINESS     = 'business';
    public const TYPE_UNVERIFIED     = 'unverified';

    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $iri;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Email
     */
    protected $email;

    /**
     * @var string|null
     *
     * @Assert\Choice({self::TYPE_RECEIVE_ONLY, self::TYPE_PERSONAL, self::TYPE_BUSINESS, self::TYPE_UNVERIFIED})
     */
    protected $type;

    /**
     * @var string|null
     *
     * @Assert\Ip
     */
    protected $ipAddress;

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
     * @return AbstractCustomer
     */
    public function setId(?string $id): AbstractCustomer
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
     * @return AbstractCustomer
     */
    public function setIri(?string $iri): AbstractCustomer
    {
        $this->iri = $iri;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

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
     * @return string|null
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * @param string|null $ipAddress
     *
     * @return $this
     */
    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }
}
