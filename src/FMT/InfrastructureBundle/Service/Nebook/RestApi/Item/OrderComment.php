<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 17:01
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class OrderComment
{
    /** @var \DateTime */
    private $date;

    /** @var string */
    private $commentor;

    /** @var string */
    private $message;

    /** @var bool */
    private $internal;

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getCommentor()
    {
        return $this->commentor;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return boolean
     */
    public function isInternal()
    {
        return $this->internal;
    }
}
