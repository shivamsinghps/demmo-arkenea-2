<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 13:17
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\CartItem;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\CartSummary;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Coupon;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\PaymentInfo;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ShippingInfo;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Shopper;

class CartSummaryMapper extends AbstractMapper
{
    public function map(array $source) : CartSummary
    {
        $result = new CartSummary();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->info = $sourceWrapper->getString("AdditionalInformation");
        $resultWrapper->step = $sourceWrapper->getInt("CheckoutStep");
        $resultWrapper->nrmpDateOfBirth = $sourceWrapper->getDate("NrmpDateOfBirth");
        $resultWrapper->nrmpGovernmentId = $sourceWrapper->getString("NrmpGovernmentId");
        $resultWrapper->nrmpIssuingState = $sourceWrapper->getString("NrmpIssuingState");
        $resultWrapper->discountTotal = $this->toIntPrice($sourceWrapper->getString("DiscountTotal"));
        $resultWrapper->orderTotal = $this->toIntPrice($sourceWrapper->getString("OrderTotal"));
        $resultWrapper->shippingTotal = $this->toIntPrice($sourceWrapper->getString("ShippingTotal"));
        $resultWrapper->taxTotal = $this->toIntPrice($sourceWrapper->getString("TaxTotal"));
        $resultWrapper->subTotal = $this->toIntPrice($sourceWrapper->getString("SubTotal"));
        $resultWrapper->items = $this->mapper->mapList($sourceWrapper->get("CartItems") ?: [], CartItem::class);
        $resultWrapper->shopperId = $sourceWrapper->getString("ShopperId");
        $resultWrapper->studentId = $sourceWrapper->getString("StudentId");

        if ($coupons = $sourceWrapper->get("Coupons")) {
            $resultWrapper->coupons = $this->mapper->mapList($coupons, Coupon::class);
        }

        if ($payment = $sourceWrapper->get("PaymentInformation")) {
            $resultWrapper->paymentInfo = $this->mapper->map($payment, PaymentInfo::class);
        }

        if ($shipping = $sourceWrapper->get("ShippingInformation")) {
            $resultWrapper->shippingInfo = $this->mapper->map($shipping, ShippingInfo::class);
        }

        if ($shopper = $sourceWrapper->get("Shopper")) {
            $resultWrapper->shopper = $this->mapper->map($shopper, Shopper::class);
        }

        return $result;
    }
}
