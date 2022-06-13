<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 11:25
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

/**
 * Class CartSummary
 * @package FMT\InfrastructureBundle\Service\Nebook\RestApi\Item
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CartSummary
{
    const STEP_SHIPPING_INFO = "ShippingInformation";
    const STEP_PAYMENT_INFO = "PaymentInformation";
    const STEP_SUBMIT_ORDER = "SubmitOrder";

    /** @var string */
    private $info;

    /** @var int */
    private $step;

    /** @var \DateTime */
    private $nrmpDateOfBirth;

    /** @var string */
    private $nrmpGovernmentId;

    /** @var string */
    private $nrmpIssuingState;

    /** @var int */
    private $discountTotal;

    /** @var int */
    private $orderTotal;

    /** @var int */
    private $shippingTotal;

    /** @var int */
    private $taxTotal;

    /** @var int */
    private $subTotal;

    /** @var CartItem[] */
    private $items;

    /** @var Coupon[] */
    private $coupons;

    /** @var PaymentInfo */
    private $paymentInfo;

    /** @var ShippingInfo */
    private $shippingInfo;

    /** @var string */
    private $shopperId;

    /** @var string */
    private $studentId;

    /** @var Shopper */
    private $shopper;

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @return \DateTime
     */
    public function getNrmpDateOfBirth()
    {
        return $this->nrmpDateOfBirth;
    }

    /**
     * @return string
     */
    public function getNrmpGovernmentId()
    {
        return $this->nrmpGovernmentId;
    }

    /**
     * @return string
     */
    public function getNrmpIssuingState()
    {
        return $this->nrmpIssuingState;
    }

    /**
     * @return int
     */
    public function getDiscountTotal()
    {
        return $this->discountTotal;
    }

    /**
     * @return int
     */
    public function getOrderTotal()
    {
        return $this->orderTotal;
    }

    /**
     * @return int
     */
    public function getShippingTotal()
    {
        return $this->shippingTotal;
    }

    /**
     * @return int
     */
    public function getTaxTotal()
    {
        return $this->taxTotal;
    }

    /**
     * @return int
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @return CartItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return Coupon[]
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * @return PaymentInfo
     */
    public function getPaymentInfo()
    {
        return $this->paymentInfo;
    }

    /**
     * @return ShippingInfo
     */
    public function getShippingInfo()
    {
        return $this->shippingInfo;
    }

    /**
     * @return string
     */
    public function getShopperId()
    {
        return $this->shopperId;
    }

    /**
     * @return string
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @return Shopper
     */
    public function getShopper()
    {
        return $this->shopper;
    }
}
