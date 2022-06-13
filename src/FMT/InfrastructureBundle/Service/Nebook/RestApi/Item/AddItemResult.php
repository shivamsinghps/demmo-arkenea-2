<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 13:09
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class AddItemResult
{
    const RESULT_SUCCESS = "Success";
    const RESULT_ERROR = "Error";
    const RESULT_PARTIAL_ADD = "PartialAdd";

    /** @var string */
    private $familyId;

    /** @var string */
    private $sku;

    /** @var int */
    private $result;

    /** @var string */
    private $message;

    /**
     * @return string
     */
    public function getFamilyId()
    {
        return $this->familyId;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
