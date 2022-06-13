<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

/**
 * Class Clearing
 */
class Clearing
{
    public const SOURCE_STANDARD = 'standard';
    public const SOURCE_NEXT_AVAILABLE = 'next-available';
    public const DESTINATION_NEXT_AVAILABLE = 'next-available';
    
    /**
     * @var string|null
     */
    protected $source;

    /**
     * @var string|null
     */
    protected $destination;

    /**
     * @param string|null $source
     * @param string|null $destination
     */
    public function __construct(?string $source = null, ?string $destination = null)
    {
        $this->source = $source;
        $this->destination = $destination;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param string|null $source
     *
     * @return Clearing
     */
    public function setSource(?string $source): Clearing
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDestination(): ?string
    {
        return $this->destination;
    }

    /**
     * @param string|null $destination
     *
     * @return Clearing
     */
    public function setDestination(?string $destination): Clearing
    {
        $this->destination = $destination;

        return $this;
    }
}
