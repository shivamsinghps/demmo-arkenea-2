<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Addenda
 */
class Addenda
{
    /**
     * @var string[]
     *
     * @Assert\All({
     *     @Assert\Length(min=1, max=80),
     *     @Assert\Regex("/[a-zA-Z0-9-_.~!*'();:@&=+$,\/?%#[\]]+/"),
     * })
     */
    protected $values;

    /**
     * @param string[] $values
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * @return string[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param string[] $values
     *
     * @return Addenda
     */
    public function setValues(array $values): Addenda
    {
        $this->values = $values;

        return $this;
    }
}
