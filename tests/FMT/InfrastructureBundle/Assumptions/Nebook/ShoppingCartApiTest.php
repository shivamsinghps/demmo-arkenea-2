<?php

namespace Tests\FMT\InfrastructureBundle\Assumptions\Nebook;

use FMT\InfrastructureBundle\Service\Nebook\RestApi\Client;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Exception;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\{Address,
    CartItem,
    CartSummary,
    PaymentInfo,
    PaymentMethod,
    ShippingInfo,
    Shopper};
use Tests\FMT\InfrastructureBundle\AbstractTest;
use Tests\FMT\InfrastructureBundle\Assumptions\Nebook\Traits\CartHelperTrait;
use Tests\FMT\InfrastructureBundle\Assumptions\Nebook\Traits\ModelValidatorTrait;

/**
 * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help
 *
 * Class ShoppingCartApiTest
 * @package Tests\FMT\InfrastructureBundle\Assumptions
 */
class ShoppingCartApiTest extends AbstractTest
{
    use ModelValidatorTrait, CartHelperTrait;

    const CART_ITEMS_NUMBER = 2;

    /**
     * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help/operations/ShopperCreate
     */
    public function testShopperCreate()
    {
        $email = sprintf('test-%d@gmail.com', microtime(true));

        $billingAddress = $this->generateAddress(1);
        $shippingAddress = $this->generateAddress(2);

        $shopper = new Shopper();
        $shopper->setStudentId('123');
        $shopper->setEmail($email);
        $shopper->setPassword('pasSW11!!');
        $shopper->setAllowBuybackEmail(true);
        $shopper->setAllowDirectEmail(true);
        $shopper->setBillingAddress($billingAddress);
        $shopper->setDisabled(false);
        $shopper->setMembershipId('123');
        $shopper->setShippingAddress($shippingAddress);
        $shopper->setTaxExempt(true);

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $client->shopperCreate($shopper);

        $createdShopper = $client->shopperGetByEmail($email);

        $this->assertNotEmpty($createdShopper);
        $this->isSameShopper($shopper, $createdShopper);

        return $createdShopper;
    }

    /**
     * @depends testShopperCreate
     *
     * @param Shopper $shopper
     * @return Shopper
     */
    public function testShopperUpdate(Shopper $shopper)
    {
        $email = sprintf('test2-%d@gmail.com', microtime(true));

        $billingAddress = $this->generateAddress(3);
        $shippingAddress = $this->generateAddress(4);

        $shopper->setStudentId('1234');
        $shopper->setEmail($email);
        $shopper->setPassword('pasSW11!!2');
        $shopper->setAllowBuybackEmail(false);
        $shopper->setAllowDirectEmail(false);
        $shopper->setBillingAddress($billingAddress);
        $shopper->setDisabled(false);
        $shopper->setMembershipId('123');
        $shopper->setShippingAddress($shippingAddress);
        $shopper->setTaxExempt(true);

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $this->assertTrue($client->shopperUpdate($shopper->getId(), $shopper));

        $resultShopperUpdate = $client->shopperGetById($shopper->getId());

        $this->assertNotEmpty($resultShopperUpdate);

        $this->isSameShopper($shopper, $resultShopperUpdate);

        return $resultShopperUpdate;
    }

    /**
     * @depends testShopperUpdate
     *
     * @param Shopper $shopper
     * @return CartSummary
     * @throws Exception
     */
    public function testCartAddItems(Shopper $shopper)
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');

        $summary = $client->cartGetSummary($shopper->getId());
        $this->assertEmpty($summary->getItems());
        $this->assertEquals(0, $summary->getSubTotal());

        $cartItems = $this->getCartItems();
        $client->cartAddItems($shopper->getId(), $cartItems);
        $summary = $client->cartGetSummary($shopper->getId());

        $this->assertCount(self::CART_ITEMS_NUMBER, $summary->getItems());

        $skuListToInsert = array_map(function (CartItem $item) {
            return $item->getSku();
        }, $cartItems);
        $cartSkuList = array_map(function (CartItem $item) {
            return $item->getSku();
        }, $summary->getItems());

        $this->assertEquals(array_values($skuListToInsert), array_values($cartSkuList));

        return $summary;
    }

    /**
     * @depends testShopperUpdate
     *
     * @param Shopper $shopper
     * @return CartSummary
     * @throws \Exception
     */
    public function testCheckoutAddShipping(Shopper $shopper)
    {
        $shippingInstructions = 'Test instruction';

        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $shippingOption = $this->getShippingOption($client);
        $shippingCodeId = $shippingOption->getId();
        $address = $this->generateAddress(5);

        $shippingInfo = new ShippingInfo();
        $shippingInfo->setAddress($address);
        $shippingInfo->setShippingCodeId($shippingCodeId);
        $shippingInfo->setInstructions($shippingInstructions);

        $resultCheckoutAddShipping = $client->checkoutAddShipping($shopper->getId(), $shippingInfo);

        $this->assertEquals($address, $resultCheckoutAddShipping->getShippingInfo()->getAddress());
        $this->assertEquals($shippingCodeId, $resultCheckoutAddShipping->getShippingInfo()->getShippingCodeId());
        $this->assertEquals($shippingInstructions, $resultCheckoutAddShipping->getShippingInfo()->getInstructions());

        return $resultCheckoutAddShipping;
    }

    /**
     * @depends testShopperUpdate
     *
     * @param Shopper $shopper
     * @return CartSummary
     */
    public function testCheckoutAddPayment(Shopper $shopper)
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $tenders = $client->tendersGetAll();

        $accountNumber = '44620300000000004917610000000000003';
        $tenderId = $tenders[0]->getId();
        $address = $this->generateAddress(6);

        $paymentMethod = new PaymentMethod();
        $paymentMethod->setAccountNumber($accountNumber);
        $paymentMethod->setTenderId($tenderId);

        $paymentInfo = new PaymentInfo();
        $paymentInfo->setAddress($address);
        $paymentInfo->setMethods([$paymentMethod]);

        $resultCheckoutAddPayment = $client->checkoutAddPayment($shopper->getId(), $paymentInfo);

        // Note, it does not use provided address, but uses shoppers address instead!
        $this->isSameAddress($shopper->getBillingAddress(), $resultCheckoutAddPayment->getPaymentInfo()->getAddress());
        $this->assertEquals(
            $accountNumber,
            $resultCheckoutAddPayment->getPaymentInfo()->getMethods()[0]->getAccountNumber()
        );
        $this->assertEquals($tenderId, $resultCheckoutAddPayment->getPaymentInfo()->getMethods()[0]->getTenderId());

        return $resultCheckoutAddPayment;
    }

    /**
     * @depends testShopperCreate
     *
     * @param Shopper $shopper
     */
    public function testShopperGetByEmail(Shopper $shopper)
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $this->assertInstanceOf(Shopper::class,
            $client->shopperGetByEmail($shopper->getEmail())
        );
    }

    /**
     * @depends testShopperCreate
     *
     * @param Shopper $shopper
     */
    public function testShopperGetById(Shopper $shopper)
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $this->assertInstanceOf(Shopper::class,
            $client->shopperGetById($shopper->getId())
        );
    }

    /**
     * @depends testCartAddItems
     *
     * @param CartSummary $cartSummary
     */
    public function testClearCart(CartSummary $cartSummary)
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');

        $this->assertCount(self::CART_ITEMS_NUMBER, $cartSummary->getItems());
        $this->assertGreaterThan(0, $cartSummary->getSubTotal());

        $this->assertTrue(
            $client->clearCart($cartSummary->getShopperId())
        );

        $cleanCartSummary = $client->cartGetSummary($cartSummary->getShopperId());

        $this->assertEmpty($cleanCartSummary->getItems());
        $this->assertEquals(0, $cleanCartSummary->getSubTotal());
    }

    /**
     * @depends testShopperCreate
     *
     * @param Shopper $shopper
     */
    public function testCartGetSummary(Shopper $shopper)
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $cartSummary = $client->cartGetSummary($shopper->getId());
        $this->assertInstanceOf(CartSummary::class, $cartSummary);
    }

    /**
     * @depends testShopperCreate
     *
     * @param Shopper $shopper
     */
    public function testShopperDisableById(Shopper $shopper)
    {
        $this->assertFalse($shopper->isDisabled());
        $client = $this->container->get('test.infrastructure.service.nebook.client');
        $this->assertTrue(
            $client->shopperDisableById($shopper->getId())
        );

        $disabledShopper = $client->shopperGetById($shopper->getId());
        $this->assertInstanceOf(Shopper::class, $disabledShopper);
        $this->assertTrue($disabledShopper->isDisabled());
    }

//    /**
//     * @depends testShopperUpdate
//     *
//     * @param Shopper $shopper
//     * @return Order
//     */
//    public function testCheckoutSubmitOrder(Shopper $shopper)
//    {
//        $client = $this->container->get('test.infrastructure.service.nebook.client');
//        $cartSummary = $client->cartGetSummary($shopper->getId());
//        $resultCheckoutSubmitOrder = $client->checkoutSubmitOrder($shopper->getId());
//
//        //check billing address
//        $this->assertInstanceOf(Address::class, $resultCheckoutSubmitOrder->getBilling());
//        $this->isSameAddress($shopper->getBillingAddress(), $resultCheckoutSubmitOrder->getBilling());
//
//        //check shipping address
//        $this->assertInstanceOf(Address::class, $resultCheckoutSubmitOrder->getShipping());
//        $this->isSameAddress($shopper->getShippingAddress(), $resultCheckoutSubmitOrder->getShipping());
//
//        //check the same cart items
//        foreach ($resultCheckoutSubmitOrder->getItems() as $item) {
//            $this->assertInstanceOf(OrderItem::class, $item);
//        }
//
//        $this->assertEquals(self::CART_ITEMS_NUMBER, count($resultCheckoutSubmitOrder->getItems()));
//        $skuListInCart = array_map(function (CartItem $item) {
//            return $item->getSku();
//        }, $cartSummary->getItems());
//        $skuListInOrder = array_map(function (OrderItem $item) {
//            return $item->getSku();
//        }, $resultCheckoutSubmitOrder->getItems());
//        $this->assertEquals(array_values($skuListInCart), array_values($skuListInOrder));
//
//        //check payment methods
//        $cartPaymentMethodTenderIds = array_map(function (PaymentMethod $method) {
//            return $method->getTenderId();
//        }, $cartSummary->getPaymentInfo()->getPaymentMethods());
//
//        $resultPaymentMethodTenderIds = array_map(function (PaymentMethod $method) {
//            return $method->getTenderId();
//        }, $resultCheckoutSubmitOrder->getPaymentMethods());
//
//        $this->assertEquals(array_values($cartPaymentMethodTenderIds), array_values($resultPaymentMethodTenderIds));
//
//        //check shipping code
//        $cartShippingCodeId = $cartSummary->getShippingInfo()->getShippingCodeId();
//        $resultShippingCodeId = $resultCheckoutSubmitOrder->getShippingCodeId();
//        $this->assertEqual($cartShippingCodeId, $resultShippingCodeId);
//
//        //check shopper
//        $resultShopper = $resultCheckoutSubmitOrder->getShopper();
//        $this->assertInstanceOf(Shopper::class, $resultShopper);
//        $this->assertNotEmpty($resultShopper);
//        $this->isSameShopper($shopper, $resultShopper);
//
//        //check subtotal
//        $this->assertEquals($cartSummary->getSubTotal(), $resultCheckoutSubmitOrder->getSubtotal());
//
//        return $resultCheckoutSubmitOrder;
//    }

//    /**
//     * @depends testShopperUpdate
//     * @depends testCheckoutAddPayment
//     *
//     * @param Shopper $shopper
//     * @param CartSummary $cartSummary
//     * @return CartSummary
//     * @throws Exception
//     */
//    public function testCheckoutVerifyOrder(Shopper $shopper, CartSummary $cartSummary)
//    {
//        $client = $this->container->get('test.infrastructure.service.nebook.client');
//        $resultCheckoutVerifyOrder = $client->checkoutVerifyOrder($shopper->getId()); //cart object
//
//        //$cartSummary vs $resultCheckoutVerifyOrder
//        $this->assertEquals($cartSummary, $resultCheckoutVerifyOrder);
//
//        //check the same cart items
//        $this->assertEquals(self::CART_ITEMS_NUMBER, count($resultCheckoutVerifyOrder->getItems()));
//
//        $skuListInOrder = array_map(function (CartItem $item) {
//            return $item->getSku();
//        }, $resultCheckoutVerifyOrder->getItems());
//
//        $skuListInCart = array_map(function (CartItem $item) {
//            return $item->getSku();
//        }, $cartSummary->getItems());
//
//        $this->assertEquals(array_values($skuListInOrder), array_values($skuListInCart));
//
//        $this->assertEquals($cartSummary->getSubTotal(), $resultCheckoutVerifyOrder->getSubtotal());
//
//        //check if equal subtotal
//        //check payment info
//        //check shipping info (shipping total)
//
//        //check shopper
//        $this->isSameShopper($shopper, $resultCheckoutVerifyOrder->getShopper());
//
//        //$this->assertEquals($shopper, $resultCheckoutVerifyOrder->getShopper());
//
//        // TODO: ADD tests
//
//        return $resultCheckoutVerifyOrder;
//    }
//
//    /**
//     * @depends testShopperUpdate
//     * @depends testCheckoutSubmitOrder
//     *
//     * @param Shopper $shopper
//     * @param Order $order
//     * @return Order
//     */
//    public function testOrderGetById(Shopper $shopper, Order $order)
//    {
//        $client = $this->container->get('test.infrastructure.service.nebook.client');
//        $resultOrderGetById = $client->orderGetById($order->getId());
//
//        $this->assertEquals($order, $resultOrderGetById);
//
//        // TODO: ADD tests
//
//        return $resultOrderGetById;
//    }
//
//    /**
//     * @depends testShopperUpdate
//     * @depends testCheckoutSubmitOrder
//     *
//     * @param Shopper $shopper
//     * @param Order $order
//     */
//    public function testOrderGetByUniqueId(Shopper $shopper, Order $order)
//    {
//        $client = $this->container->get('test.infrastructure.service.nebook.client');
//        $resultOrderGetByUniqueId = $client->orderGetByUniqueId($order->getId());
//
//        $this->assertEquals($order, $resultOrderGetByUniqueId);
//
//        // TODO: ADD tests
//    }

    /**
     * @param Client $client
     * @return object
     * @throws Exception
     */
    protected function getShippingOption(Client $client)
    {
        $list = $client->shippingCodesGetAll();

        return $list[0];
    }

    /**
     * @param int $uniqueNumber
     * @return Address
     */
    private function generateAddress(int $uniqueNumber)
    {
        $address = new Address();
        $address->setAddress1('Some st., 1524-12' . $uniqueNumber);
        $address->setAddress2(null);
        $address->setCity('Lincoln' . $uniqueNumber);
        $address->setCountry('US');
        $address->setPhone('40' . sprintf('%08d', $uniqueNumber));
        $address->setState('NE');
        $address->setZip('6' . sprintf('%04d', $uniqueNumber));
        $address->setFirstName('Example' . sprintf('%08d', $uniqueNumber));
        $address->setLastName('Test' . sprintf('%08d', $uniqueNumber));

        return $address;
    }

    /**
     * @return CartItem[]
     * @throws Exception
     */
    private function getCartItems()
    {
        $client = $this->container->get('test.infrastructure.service.nebook.client');

        $terms = $client->termsGetOpened();
        $term = $terms[0];

        return $this->getItems($client, $term, self::CART_ITEMS_NUMBER);
    }
}
