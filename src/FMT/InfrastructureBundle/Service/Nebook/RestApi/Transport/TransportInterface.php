<?php
/**
 * Author: Anton Orlov
 * Date: 27.02.2018
 * Time: 14:12
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Transport;

use FMT\InfrastructureBundle\Service\Nebook\Options;

/**
 * Interface TransportInterface
 * @package FMT\InfrastructureBundle\Service\Nebook\RestApi\Transport
 */
interface TransportInterface
{
    /**
     * TransportInterface constructor.
     * @param Options $options
     */
    public function __construct(Options $options);

    /**
     * @param string $method
     * @param string|array $args
     * @return array
     * @throws TransportException
     * @throws NotFoundException
     */
    public function get($method, $args = null);

    /**
     * @param string $method
     * @param string|array $payload
     * @param string|array $args
     * @return array|string
     * @throws TransportException
     * @throws NotFoundException
     */
    public function post($method, $payload, $args = null);
}
