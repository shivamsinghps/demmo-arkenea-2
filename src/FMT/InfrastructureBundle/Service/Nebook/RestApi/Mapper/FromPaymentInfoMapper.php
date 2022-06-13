<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 14:26
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\PaymentInfo;

class FromPaymentInfoMapper extends AbstractMapper
{
    public function map(PaymentInfo $source) : array
    {
        $result = [
            "AdditionalTenders" => [],
            "BillingAddress" => $this->mapper->map($source->getAddress(), "array"),
            "MainTender" => null
        ];

        if ($method = $source->getFirstMethod()) {
            $result["MainTender"]["AccountNumber"] = $method->getAccountNumber();
            $result["MainTender"]["TenderId"] = $method->getTenderId();
            $result["MainTender"]["CardCode"] = $method->getCardCode();
            $result["MainTender"]["Expiration"] = $method->getExpirationDate();
            $result["MainTender"]["FirstName"] = $method->getFirstName();
            $result["MainTender"]["LastFour"] = $method->getCardNumber();
            $result["MainTender"]["LastName"] = $method->getLastName();
        }

        return $result;
    }
}
