<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait CorrelationTrait
 */
trait CorrelationTrait
{
    /**
     * @var string|null
     *
     * @Assert\Length(min=1, max=255)
     * @Assert\Regex(pattern='[a-z0-9-_.]+')
     */
    protected $correlationId;

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
     * @return $this
     */
    public function setCorrelationId(?string $correlationId): self
    {
        $this->correlationId = $correlationId;

        return $this;
    }
}
