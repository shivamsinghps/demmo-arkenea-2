<?php

namespace FMT\DataBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ShippingOption
 * @package FMT\DataBundle\Model
 */
class ShippingOption
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
