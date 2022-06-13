<?php
/**
 * Author: Anton Orlov
 * Date: 02.04.2018
 * Time: 16:40
 */

namespace FMT\InfrastructureBundle\Service\AmazonS3;

use Aws\S3\S3Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StorageFactory
{
    /** @var array */
    private static $typeMapping = [
        "file" => "getLocalStorage",
        "s3" => "getAmazonStorage"
    ];

    /** @var string */
    private $webRoot;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct($root, UrlGeneratorInterface $urlGenerator)
    {
        $this->webRoot = realpath($root);
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param string $descriptor
     * @param string|null $key
     * @param string|null $secret
     * @return StorageInterface
     */
    public function getInstance(string $descriptor, $key, $secret)
    {
        $components = parse_url($descriptor);

        if (!isset($components["scheme"])) {
            throw new \InvalidArgumentException(sprintf("Inappropriate storage descriptor (%s)", $descriptor));
        }

        $type = strtolower($components["scheme"]);

        if (!array_key_exists($type, self::$typeMapping)) {
            throw new \InvalidArgumentException(sprintf("Unsupported descriptor type (%s)", $type));
        }

        $components["key"] = $key;
        $components["secret"] = $secret;

        return call_user_func([$this, self::$typeMapping[$type]], $components);
    }

    /**
     * @param array $descriptor
     * @return LocalStorage
     * @throws \Exception
     */
    protected function getLocalStorage($descriptor)
    {
        if (!isset($descriptor["path"])) {
            throw new \InvalidArgumentException("Path to local storage is not defined");
        }

        $path = str_replace("/", DIRECTORY_SEPARATOR, urldecode($descriptor["path"]));

        return new LocalStorage($this->webRoot, $path, $this->urlGenerator);
    }

    /**
     * @param array $descriptor
     * @return AmazonStorage
     */
    protected function getAmazonStorage($descriptor)
    {
        $options = [
            "version" => AmazonStorage::VERSION,
            "region" => $descriptor["host"] ?? "undefined"
        ];

        if (isset($descriptor["key"]) && isset($descriptor["secret"])) {
            $options["credentials"] = [
                "key" => $descriptor["key"],
                "secret" => $descriptor["secret"],
            ];
        } else {
            $options["credentials"] = new DefaultCacheAdapter();
        }

        return new AmazonStorage(new S3Client($options), $descriptor["path"]);
    }
}
