<?php
/**
 * Author: Anton Orlov
 * Date: 03.03.2018
 * Time: 17:01
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Shopper;

class FromShopperMapper extends AbstractMapper
{
    public function map(Shopper $source) : array
    {
        return [
            "AllowBuybackEmail" => (bool) $source->isAllowBuybackEmail(),
            "AllowDirectEmail" => (bool) $source->isAllowDirectEmail(),
            "BillingAddress" => $this->mapper->map($source->getBillingAddress(), "array"),
            "Email" => $source->getEmail(),
            "IsDisabled" => (bool) $source->isDisabled(),
            "IsTaxExempt" => (bool) $source->isTaxExempt(),
            "MembershipId" => $source->getMembershipId(),
            "Password" => $source->getPassword(),
            "ShippingAddress" => $this->mapper->map($source->getShippingAddress(), "array"),
            "StudentId" => $source->getStudentId()
        ];
    }
}
