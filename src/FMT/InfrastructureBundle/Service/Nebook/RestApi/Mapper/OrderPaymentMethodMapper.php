<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 16:57
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\OrderPaymentMethod;

class OrderPaymentMethodMapper extends AbstractMapper
{
    public function map(array $source) : OrderPaymentMethod
    {
        $result = new OrderPaymentMethod();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->accountInfo = $sourceWrapper->getString("AccountInformation");
        $resultWrapper->amount = $this->toIntPrice($sourceWrapper->getString("Amount"));
        $resultWrapper->creditCard = $sourceWrapper->getBool("IsCreditCard");
        $resultWrapper->name = $sourceWrapper->getString("Name");

        return $result;
    }
}
