<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 17:30
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class SubmitOrderResult
{
    /** @var int */
    private $response;

    /** @var string */
    private $message;

    /** @var Order */
    private $order;

    /**
     * @return int
     */
    public function getResponse(): int
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}
