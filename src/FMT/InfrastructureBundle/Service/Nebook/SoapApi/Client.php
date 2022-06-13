<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi;

use FMT\InfrastructureBundle\Service\Nebook\Mapper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Transport\TransportInterface;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\Order;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\TaxMethod;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\TaxShipping;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\OrderInfo;

/**
 * Class Client
 * @package FMT\InfrastructureBundle\Service\Nebook\SoapApi
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Client
{
    /** @var string */
    private $siteId;

    /** @var TransportInterface */
    private $transport;

    /**
     * __construct
     * Client constructor.
     * @param TransportInterface $transport
     * @param string $siteId
     */
    public function __construct(TransportInterface $transport, string $siteId)
    {
        $this->transport = $transport;
        $this->siteId = $siteId;
    }

    /**
     * Method returns all tax methods for a given site.
     *
     * @return TaxMethod[]
     */
    public function getTax()
    {
        $method = 'GetTax';
        $methodError = 'ErrHandler';
        $body = [
            'SiteID' => $this->siteId
        ];

        $response = $this->transport->post($method, $methodError, [$method => $body]);

        return array_map(function ($item) {
            return Mapper::map($item, TaxMethod::class, Mapper::DIR_CLIENT_SOAP_API);
        }, $response['GetTaxResult']['TaxItems']);
    }

    /**
     * Method returns all shipping methods and the tax value for a state.
     * 
     * @param string $state
     * @return TaxShipping[]
     */
    public function getTaxShipping(string $state)
    {
        $method = 'GetTaxShipping';
        $methodError = 'ErrHandler';
        $body = [
            'SiteID' => $this->siteId,
            'State' => $state
        ];

        $response = $this->transport->post($method, $methodError, [$method => $body]);

        return array_map(function ($item) {
            return Mapper::map($item, TaxShipping::class, Mapper::DIR_CLIENT_SOAP_API);
        }, $response);
    }

    /**
     * Method creates a marketplace order within the given sites database. Returns OrderNumber for the store or any errors encountered in the order pipeline.
     *
     * @param Order $order
     * @return OrderInfo[]
     */
    public function pushOrder(Order $order)
    {
//        $method = 'PushOrder';
//        $methodError = 'ErrHandler';
//        $body = [
//            'SiteID' => $this->siteId,
//            'OrderInput' => Mapper::map($order, "array", Mapper::DIR_CLIENT_SOAP_API)
//        ];
//
//        $response = $this->transport->post($method, $methodError, [$method => $body]);
//
//        return array_map(function ($item) {
//            return Mapper::map($item, OrderInfo::class, Mapper::DIR_CLIENT_SOAP_API);
//        }, $response);
        $orderInfo = new OrderInfo();
        $orderInfo->setStatus('cart');
        $orderInfo->setId(random_int(1, PHP_INT_MAX));
        return ["PushOrderResult" => $orderInfo];
    }

    /**
     * Method returns the status of an order based on the site id, email and order id.
     *
     * @param string $email
     * @param string $orderId
     * @return OrderInfo[]
     */
    public function checkStatus(string $email, string $orderId)
    {
        $method = 'CheckStatus';
        $methodError = 'OrderStatusError';
        $body = [
            'SiteID' => $this->siteId,
            'Email' => $email,
            'OrderID' => $orderId,
        ];

        $response = $this->transport->post($method, $methodError, [$method => $body]);

        return array_map(function ($item) {
            return Mapper::map($item, OrderInfo::class, Mapper::DIR_CLIENT_SOAP_API);
        }, $response);
    }
}
