<?php

namespace Tests\FMT\DomainBundle\Service\Cart;

use FMT\DomainBundle\Service\Cart\CheckoutService;
use Tests\FMT\InfrastructureBundle\AbstractTest;

/**
 * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help
 *
 * Class CheckoutTest
 * @package Tests\FMT\DomainBundle\Service\Cart
 */
class CheckoutTest extends AbstractTest
{
    /**
     * @var CheckoutService
     */
    private $checkoutService;

    public function setUp()
    {
        parent::setUp();

        $this->checkoutService = $this->container->get('test.domain.service.cart.checkout');
    }

    /**
     * @param string $shopperId
     * @param bool $expected
     *
     * @dataProvider shopperIdForCheckout
     */
    public function testCheckout($shopperId, $expected)
    {
        $result = $this->checkoutService->checkout($shopperId);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function shopperIdForCheckout()
    {
        return [
//            TODO: Edit when Nebook Checkout API method will be fixed
//            [
//                'shopperId' => '3bb863c275cf4ee5a80e017ef914c2d3',
//                'expected' => true,
//            ],
            [
                'shopperId' => 'xxxx',
                'expected' => false,
            ],
        ];
    }
}
