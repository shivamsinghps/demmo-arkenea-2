<?php
/**
 * Author: Anton Orlov
 * Date: 03.03.2018
 * Time: 19:08
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Address;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Shopper;

/**
 * Class ToShopperMapper
 * @package FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper
 */
class ToShopperMapper extends AbstractMapper
{
    /**
     * @param array $source
     * @return Shopper
     */
    public function map(array $source): Shopper
    {
        $result = new Shopper();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getString("Id");
        $resultWrapper->shopperNumber = $sourceWrapper->getInt("ShopperNumber");
        $resultWrapper->studentId = $sourceWrapper->getString("StudentId");
        $resultWrapper->membershipId = $sourceWrapper->getString("MembershipId");
        $resultWrapper->created = $sourceWrapper->getDate("DateCreated");
        $resultWrapper->email = $sourceWrapper->getString("Email");
        $resultWrapper->isDisabled = $sourceWrapper->getBool("IsDisabled");
        $resultWrapper->isTaxExempt = $sourceWrapper->getBool("IsTaxExempt");
        $resultWrapper->allowBuybackEmail = $sourceWrapper->getBool("AllowBuybackMail");
        $resultWrapper->allowDirectEmail = $sourceWrapper->getBool("AllowDirectMail");
        $resultWrapper->billingAddress = $this->mapper->map($sourceWrapper->get("BillingAddress"), Address::class);
        $resultWrapper->shippingAddress = $this->mapper->map($sourceWrapper->get("ShippingAddress"), Address::class);

        return $result;
    }
}
