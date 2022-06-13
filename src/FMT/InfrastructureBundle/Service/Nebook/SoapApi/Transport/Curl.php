<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Transport;

use FMT\InfrastructureBundle\Helper\ArrayHelper;
use FMT\InfrastructureBundle\Service\Nebook\Options;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Exception;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\CustomSoapClient;
use SoapHeader;
use SoapFault;

/**
 * Class Curl
 * @package FMT\InfrastructureBundle\Service\Nebook\SoapApi\Transport
 */
class Curl implements TransportInterface
{
    /** @var Options */
    private $options;

    /**
     * __construct
     * Curl constructor.
     * @see TransportInterface::__construct
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    /**
     * Set request header.
     * @return SoapHeader
     */
    private function setRequestHeaders()
    {
        static $soapParameters = null;

        if (empty($soapParameters)) {
            $soapParameters = [
                'username' => $this->options->username,
                'password' => $this->options->password
            ];
        }

        $header = new SoapHeader($this->options->xmlns, 'UserCredentials', $soapParameters, false);

        return $header;
    }

    /**
     * @see TransportInterface::post
     * @param string $method
     * @param string $methodError
     * @param string|array $payload
     * @return array
     * @throws TransportException
     * @throws Exception
     */
    public function post($method, $methodError, $payload)
    {
        $ctx_opts = array(
            'http' => array(
                'header' => 'Content-Type: text/xml; charset=utf-8'
            )
        );
        
        $ctx = stream_context_create($ctx_opts);

        $options = [
            'soap_version' => SOAP_1_1,
            'trace' => true,
            'connection_timeout' => $this->options->timeout,
            'xmlns' => $this->options->xmlns,
            'stream_context' => $ctx
        ];

        $soapClient = new CustomSoapClient($this->options->wsdl, $options);
        $soapClient->__setSoapHeaders($this->setRequestHeaders());

        try {
            $response = $soapClient->__soapCall($method, $payload);
        } catch (SoapFault $errorMessage) {
            throw new TransportException($errorMessage);
        }

        // Nebook SOAP API returns '' body with 200 OK status if object was not found
        if (count(get_object_vars($response)) === 0) {
            return null;
        }

        if (!is_object($response) || ($result = ArrayHelper::objectToArray($response)) === null) {
            throw new TransportException("Unsupported response format");
        }

        $methodResult = $result[$method . 'Result'];
        $errHandler = $methodResult[$methodError];
        if ($errHandler['ErrorNum'] !== 0) {
            throw new Exception($errHandler['ErrorDesc']);
        }

        return $result;
    }
}
