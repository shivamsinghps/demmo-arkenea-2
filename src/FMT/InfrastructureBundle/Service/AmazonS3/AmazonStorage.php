<?php
/**
 * Author: Anton Orlov
 * Date: 02.04.2018
 * Time: 16:40
 */

namespace FMT\InfrastructureBundle\Service\AmazonS3;

use Aws\Result;
use Aws\S3\S3Client;
use FMT\InfrastructureBundle\Helper\FileHelper;
use GuzzleHttp\Psr7\Stream;

class AmazonStorage implements StorageInterface
{
    const VERSION = "2006-03-01";

    /** @var S3Client */
    private $client;

    /** @var string */
    private $bucket;

    /** @var string */
    private $prefix;

    public function __construct(S3Client $client, $bucket)
    {
        $this->client = $client;

        $components = explode("/", trim(str_replace("\\", "/", urldecode($bucket)), "/"), 2);

        if (!S3Client::isBucketDnsCompatible($components[0])) {
            throw new \Exception("Invalid bucket name");
        }

        $this->bucket = $components[0];

        if (count($components) > 1) {
            $this->prefix = trim($components[1], "/");
            if (empty($this->prefix)) {
                $this->prefix = null;
            }
        }
    }

    /**
     * @param string|resource|\SplFileInfo $source
     * @param string $destination
     * @return int
     */
    public function upload($source, string $destination)
    {
        $stream = new Stream(FileHelper::open($source, "rb"));
        $this->client->putObject([
            "ACL" => "public-read",
            "Body" => $stream,
            "Bucket" => $this->bucket,
            "Key" => $this->buildKey($destination)
        ]);
        return $stream->getSize();
    }

    /**
     * @param string $source
     * @param null|string|resource|\SplFileInfo $destination
     * @return int
     */
    public function download(string $source, $destination)
    {
        /** @var Result $response */
        $response = $this->client->getObject([
            "Bucket" => $this->bucket,
            "Key" => $this->buildKey($source)
        ]);
        return FileHelper::copy($response["Body"], $destination);
    }
    /**
     * @param string $source
     * @return bool
     */
    public function delete(string $source)
    {
        $this->client->deleteObject([
            "Bucket" => $this->bucket,
            "Key" => $this->buildKey($source)
        ]);
        return true;
    }

    /**
     * @param string $path
     * @return \Generator
     */
    public function glob(string $path)
    {
        $options = [
            "Bucket" => $this->bucket,
            "Prefix" => $this->buildKey($path)
        ];

        do {
            $result = $this->client->listObjectsV2($options);
            foreach ($result["Contents"] as $item) {
                if (strpos($item["Key"], "/") === false) {
                    yield $item["Key"];
                }
            }
            $options["ContinuationToken"] = isset($result["NextContinuationToken"]) ?? $result["NextContinuationToken"];
        } while (!empty($options["ContinuationToken"]));
    }

    /**
     * @param string $path
     * @return string
     */
    public function url(string $path)
    {
        return $this->client->getObjectUrl($this->bucket, $this->buildKey($path));
    }

    /**
     * @param string $path
     * @return string
     */
    public function getFilePath(string $path): string
    {
        return $this->url($path);
    }

    private function buildKey($key)
    {
        $result = trim(str_replace("\\", "/", $key), "/");
        if (!empty($this->prefix)) {
            $result = $this->prefix . "/" . $result;
        }
        return $result;
    }
}
