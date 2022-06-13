<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi;

use SoapClient;
use DomDocument;

/**
 * Class Client
 * @package FMT\InfrastructureBundle\Service\Nebook\SoapApi
 *
 */
class CustomSoapClient extends SoapClient
{
    /** @var Options */
    private $options;

    /**
     * __construct
     * CustomSoapClient constructor.
     * @param mixed $wsdl NULL for non-wsdl mode or URL string for wsdl mode
     * @param array $options
     */
    public function __construct($wsdl, $options)
    {
        $this->options = $options;

        parent::__construct($wsdl, $options);
    }

    /**
     * __doRequest
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int $version
     * @param int $oneWay
     * @return string
     */
    public function __doRequest($request, $location, $action, $version, $oneWay = 0)
    {
        $request = preg_replace('/SOAP-ENV/', 'soap', $request);
        $request = preg_replace('/<ns1:/', '<', $request);
        $request = preg_replace('/ns1:/', '', $request);
        $request = str_replace(['/ns1:'], ['/'], $request);
        $request = preg_replace('/ xmlns:ns1="(.*)">/', '>', $request, 1);

        $xmlns = $this->options['xmlns'];
        $xsi = 'http://www.w3.org/2001/XMLSchema-instance';
        $xsd = 'http://www.w3.org/2001/XMLSchema';

        $dom = new DomDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($request);

        $nodes = $dom->getElementsByTagName('Envelope');
        foreach ($nodes as $node) {
            $node->setAttribute('xmlns:xsi', $xsi);
            $node->setAttribute('xmlns:xsd', $xsd);
        }

        $nodes = $dom->getElementsByTagName('Body');
        foreach ($nodes as $node) {
            $childNodes = $node->childNodes;

            if (!$childNodes) continue;

            foreach ($childNodes as $curNode) {
                $curNode->setAttribute('xmlns', $xmlns);
            }
        }

        $nodes = $dom->getElementsByTagName('UserCredentials');
        foreach ($nodes as $node) {
            $node->setAttribute('xmlns', $xmlns);
        }

        $request = $dom->saveXML();

        return parent::__doRequest($request, $location, $action, $version, $oneWay);
    }
}

