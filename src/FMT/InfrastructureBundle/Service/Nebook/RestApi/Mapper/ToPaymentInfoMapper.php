<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 13:40
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Address;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\PaymentInfo;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\PaymentMethod;

class ToPaymentInfoMapper extends AbstractMapper
{
    public function map(array $source) : PaymentInfo
    {
        $result = new PaymentInfo();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->address = $this->mapper->map($sourceWrapper->get("BillingAddress"), Address::class);
        $resultWrapper->methods = $this->mapper->mapList($sourceWrapper->get("Tenders", []), PaymentMethod::class);

        return $result;
    }
}
