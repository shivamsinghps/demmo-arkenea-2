<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 11:33
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class Campus
{
    /** @var int */
    private $id;

    /** @var string */
    private $backofficeId;

    /** @var string */
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
    public function getBackofficeId()
    {
        return $this->backofficeId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
