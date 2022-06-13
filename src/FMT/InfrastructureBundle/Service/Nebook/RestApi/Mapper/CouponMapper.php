<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 13:35
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Coupon;

class CouponMapper extends AbstractMapper
{
    public function map(array $source) : Coupon
    {
        $result = new Coupon();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->code = $sourceWrapper->getString("Code");
        $resultWrapper->name = $sourceWrapper->getString("Name");
        $resultWrapper->scope = $sourceWrapper->getInt("Scope");
        $resultWrapper->amount = $this->toIntPrice($sourceWrapper->getString("OriginalAmount"));
        $resultWrapper->discount = $sourceWrapper->getFloat("TotalDiscount");

        return $result;
    }
}
