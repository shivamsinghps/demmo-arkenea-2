<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AchDetails
 */
class AchDetails
{
    /**
     * @var Addenda|null
     *
     * @Assert\Valid
     */
    protected $source;

    /**
     * @var Addenda|null
     *
     * @Assert\Valid
     */
    protected $destination;

    /**
     * @param Addenda|null $source
     * @param Addenda|null $destination
     */
    public function __construct(?Addenda $source = null, ?Addenda $destination = null)
    {
        $this->source = $source;
        $this->destination = $destination;
    }

    /**
     * @return Addenda|null
     */
    public function getSource(): ?Addenda
    {
        return $this->source;
    }

    /**
     * @param Addenda|null $source
     *
     * @return AchDetails
     */
    public function setSource(?Addenda $source): AchDetails
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return Addenda|null
     */
    public function getDestination(): ?Addenda
    {
        return $this->destination;
    }

    /**
     * @param Addenda|null $destination
     *
     * @return AchDetails
     */
    public function setDestination(?Addenda $destination): AchDetails
    {
        $this->destination = $destination;

        return $this;
    }
}
