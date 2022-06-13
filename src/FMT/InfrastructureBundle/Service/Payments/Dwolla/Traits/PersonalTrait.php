<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits;

/**
 * Trait PersonalTrait
 */
trait PersonalTrait
{
    use AddressTrait;

    /**
     * @var string
     */
    protected $ssn;

    /**
     * @return string
     */
    public function getSsn(): string
    {
        return $this->ssn;
    }

    /**
     * @param string $ssn
     *
     * @return $this
     */
    public function setSsn(string $ssn): self
    {
        $this->ssn = $ssn;

        return $this;
    }
}
