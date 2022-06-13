<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits;

/**
 * Trait BusinessTrait
 */
trait BusinessTrait
{
    /**
     * @var string
     */
    protected $businessName;

    /**
     * @var string|null
     */
    protected $doingBusinessAs;

    /**
     * @var string
     */
    protected $businessType;

    /**
     * @var string
     */
    protected $businessClassification;

    /**
     * @var string|null
     */
    protected $ein;

    /**
     * @var string|null
     */
    protected $website;

    /**
     * @return string
     */
    public function getBusinessName(): string
    {
        return $this->businessName;
    }

    /**
     * @param string $businessName
     *
     * @return $this
     */
    public function setBusinessName(string $businessName): self
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDoingBusinessAs(): ?string
    {
        return $this->doingBusinessAs;
    }

    /**
     * @param string|null $doingBusinessAs
     *
     * @return $this
     */
    public function setDoingBusinessAs(?string $doingBusinessAs): self
    {
        $this->doingBusinessAs = $doingBusinessAs;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessType(): string
    {
        return $this->businessType;
    }

    /**
     * @return string
     */
    public function getBusinessClassification(): string
    {
        return $this->businessClassification;
    }

    /**
     * @param string $businessClassification
     *
     * @return $this
     */
    public function setBusinessClassification(string $businessClassification): self
    {
        $this->businessClassification = $businessClassification;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEin(): ?string
    {
        return $this->ein;
    }

    /**
     * @param string|null $ein
     *
     * @return $this
     */
    public function setEin(?string $ein): self
    {
        $this->ein = $ein;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string|null $website
     *
     * @return $this
     */
    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }
}
