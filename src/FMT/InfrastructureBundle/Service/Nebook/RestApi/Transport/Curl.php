<?php
/**
 * Author: Anton Orlov
 * Date: 27.02.2018
 * Time: 14:11
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Transport;

use FMT\InfrastructureBundle\Helper\ArrayHelper;
use FMT\InfrastructureBundle\Service\Nebook\Options;
use Psr\Log\LoggerInterface;

/**
 * Class Curl
 * @package FMT\InfrastructureBundle\Service\Nebook\RestApi\Transport
 */
class Curl implements TransportInterface
{
    /** @var Options */
    private $options;

    /** @var resource */
    private $curl;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Curl constructor.
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
        $this->curl = curl_init();
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    /**
     * @param LoggerInterface $logger
     * @required
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $method
     * @param string|array $args
     * @return array
     * @throws TransportException
     * @throws NotFoundException
     */
    public function get($method, $args = null)
    {
        return $this->setPayload(false)
            ->setRequestUrl($method, $args)
            ->execute();
    }

    /**
     * @param string $method
     * @param string|array $payload
     * @param string|array $args
     * @return array|string
     * @throws TransportException
     * @throws NotFoundException
     */
    public function post($method, $payload, $args = null)
    {
        return $this->setPayload(true, $payload)
            ->setRequestUrl($method, $args)
            ->execute();
    }

    /**
     * @return array|null
     * @throws NotFoundException
     * @throws TransportException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function execute()
    {
        $debug = null;
        if (!empty($this->logger) && ($debug = fopen("php://temp", "wb+"))) {
            curl_setopt($this->curl, CURLOPT_VERBOSE, true);
            curl_setopt($this->curl, CURLOPT_STDERR, $debug);
        } else {
            curl_setopt($this->curl, CURLOPT_VERBOSE, false);
            curl_setopt($this->curl, CURLOPT_STDERR, null);
        }

        $response = curl_exec($this->curl);
        $code = (int) curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        if ($code > 299) {
            if ($debug && rewind($debug)) {
                $this->logger->debug(stream_get_contents($debug));
                $this->logger->debug($response);
            }
            $lastUrl = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
            $errorMessage = sprintf(
                "Unexpected HTTP response (%d) from Nebook API for method %s",
                $code,
                $lastUrl
            );

            if (in_array($code, [400, 404])) {
                throw new NotFoundException($errorMessage);
            } else {
                throw new TransportException($errorMessage);
            }
        }

        // Nebook API returns '' body with 200 OK status if object was not found
        if ($response === '') {
            return null;
        }

        if (($result = json_decode($response, true)) === null) {
            throw new TransportException("Unsupported response format");
        }

        return $result;
    }

    /**
     * @param bool $post
     * @param string|array $payload
     * @return $this
     */
    private function setPayload($post, $payload = null)
    {
        $headers = [];

        if ($post) {
            $data = is_null($payload) ? "" : json_encode($payload);
            $headers = [sprintf('Content-Length: %d', strlen($data))];

            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($this->curl, CURLOPT_POST, $post);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->options->timeout);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        return $this->setRequestHeaders($headers);
    }

    /**
     * @param string $method
     * @param string|array $args
     * @return $this
     */
    private function setRequestUrl($method, $args)
    {
        $result = $this->options->endpoint . '/' . $method;

        if (!empty($args)) {
            if (is_scalar($args)) {
                $result .= "/" . urlencode($args);
            } elseif (ArrayHelper::isDict($args)) {
                $result .= "?" . http_build_query($args);
            } elseif (ArrayHelper::isArray($args)) {
                $result .= "/" . join("/", array_map("urlencode", $args));
            } else {
                throw new \InvalidArgumentException("Argument should be an array or a string");
            }
        }

        curl_setopt($this->curl, CURLOPT_URL, $result);

        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    private function setRequestHeaders($headers = [])
    {
        static $static = null;

        if (empty($static)) {
            $static =         [
                sprintf("BookstoreId: %d", $this->options->bookstore_id),
                sprintf("Username: %s", $this->options->username),
                sprintf("Password: %s", $this->options->password),
                "Content-Type: application/json",
                "Expect:"
            ];
        }

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array_merge($static, $headers));

        return $this;
    }
}
