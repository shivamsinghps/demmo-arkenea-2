<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 17:07
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Address;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Order;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\OrderComment;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\OrderItem;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\OrderPaymentMethod;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Shopper;

class OrderMapper extends AbstractMapper
{
    public function map(array $source) : Order
    {
        $result = new Order();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("Id");
        $resultWrapper->uniqueId = $sourceWrapper->getString("UniqueIdentifier");
        $resultWrapper->studentId = $sourceWrapper->getString("StudentId");
        $resultWrapper->memberId = $sourceWrapper->getString("MemberId");
        $resultWrapper->type = $sourceWrapper->getInt("OrderType");
        $resultWrapper->status = $sourceWrapper->getString("OrderStatus");
        $resultWrapper->created = $sourceWrapper->getDate("DateEntered");
        $resultWrapper->modified = $sourceWrapper->getDate("DateModified");
        $resultWrapper->shipToEmail = $sourceWrapper->getString("ShipToEmail");
        $resultWrapper->referringUrl = $sourceWrapper->getString("ReferringUrl");
        $resultWrapper->subTotal = $this->toIntPrice($sourceWrapper->getString("SubTotal"));
        $resultWrapper->shippingTotal = $this->toIntPrice($sourceWrapper->getString("ShippingTotal"));
        $resultWrapper->taxTotal = $this->toIntPrice($sourceWrapper->getString("TaxTotal"));
        $resultWrapper->total = $this->toIntPrice($sourceWrapper->getString("OrderTotal"));
        $resultWrapper->shippingCodeId = $sourceWrapper->getInt("ShippingCode.Id");
        $resultWrapper->shippingCodeName = $sourceWrapper->getString("ShippingCode.Name");
        $resultWrapper->shippingInstructions = $sourceWrapper->getString("ShippingInstructions");
        $resultWrapper->original = $this->mapper->map($sourceWrapper->get("OriginalOrder"), Order::class);
        $resultWrapper->billing = $this->mapper->map($sourceWrapper->get("BillAddress"), Address::class);
        $resultWrapper->paymentMethods = $this->mapper->mapList(
            $sourceWrapper->get("PaymentMethods", []),
            OrderPaymentMethod::class
        );
        $resultWrapper->shipping = $this->mapper->map($sourceWrapper->get("ShipAddress"), Address::class);
        $resultWrapper->items = $this->mapper->mapList($sourceWrapper->get("OrderItems", []), OrderItem::class);
        $resultWrapper->comments = $this->mapper->mapList($sourceWrapper->get("Comments", []), OrderComment::class);
        $resultWrapper->shopper = $this->mapper->map($sourceWrapper->get("Shopper"), Shopper::class);

        return $result;
    }
}
