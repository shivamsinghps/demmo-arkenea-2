<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits;

/**
 * Trait StateTrait
 */
trait StateTrait
{
    /**
     * @var string
     */
    protected $state;

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return $this
     */
    public function setState(string $state): self
    {
        $this->state = $state;
        
        return $this;
    }
}
