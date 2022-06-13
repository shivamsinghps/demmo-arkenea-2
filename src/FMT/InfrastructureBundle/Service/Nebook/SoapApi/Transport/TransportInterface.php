<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Transport;

use FMT\InfrastructureBundle\Service\Nebook\Options;

/**
 * Interface TransportInterface.
 * @package FMT\InfrastructureBundle\Service\Nebook\SoapApi\Transport
 */
interface TransportInterface
{
    /**
     * __construct
     * TransportInterface constructor.
     * @param Options $options
     */
    public function __construct(Options $options);

    /**
     * @param string $method
     * @param string|array $payload
     * @param string $methodError
     * @return array|string
     * @throws TransportException
     * @throws Exception
     */
    public function post($method, $methodError, $payload);
}
