<?php
/**
 * Author: Anton Orlov
 * Date: 03.03.2018
 * Time: 18:48
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class OperationResult
{
    /** @var bool */
    private $success;

    /** @var string */
    private $message;

    /** @var object[string] */
    private $advanced = [];

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

   /**
     * @param string $info
     * @return object
     */
    public function getAdvanced($info)
    {
        return array_key_exists($info, $this->advanced) ? $this->advanced[$info] : null;
    }
}
