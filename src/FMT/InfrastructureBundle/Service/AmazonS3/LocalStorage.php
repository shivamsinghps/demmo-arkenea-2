<?php
/**
 * Author: Anton Orlov
 * Date: 02.04.2018
 * Time: 16:41
 */

namespace FMT\InfrastructureBundle\Service\AmazonS3;

use FMT\InfrastructureBundle\Helper\FileHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LocalStorage implements StorageInterface
{
    /** @var string */
    private $webRoot;

    /** @var string */
    private $path;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct($webRoot, $path, UrlGeneratorInterface $urlGenerator)
    {
        $this->webRoot = rtrim(realpath($webRoot), DIRECTORY_SEPARATOR);

        $this->path = $this->webRoot . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);

        if (!file_exists($this->path)) {
            throw new \Exception("Data directory is not exists");
        }

        $this->path = rtrim($this->path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param string|resource|\SplFileInfo $source
     * @param string $destination
     * @return int
     * @throws \Exception
     */
    public function upload($source, string $destination)
    {
        $path = $this->buildPath($destination);

        $directory = dirname($path);

        if (!file_exists($directory) && !mkdir($directory, 0777, true)) {
            throw new \Exception("Target folder could not be created");
        }

        return FileHelper::copy($source, $path);
    }

    /**
     * @param string $source
     * @param null|string|resource|\SplFileInfo $destination
     * @return int
     * @throws \Exception
     */
    public function download(string $source, $destination)
    {
        return FileHelper::copy($this->buildPath($source), $destination);
    }

    /**
     * @param string $source
     * @return bool
     */
    public function delete(string $source)
    {
        $result = false;
        $path = $this->buildPath($source);
        if (file_exists($path)) {
            $result = unlink($path);
        }

        return$result;
    }

    /**
     * @param string $path
     * @return \Generator
     */
    public function glob(string $path)
    {
        $pattern = $this->buildPath($path) . "*";
        $root = dirname($pattern);
        $result = glob($this->buildPath($path) . "*", GLOB_MARK);
        foreach ($result as $item) {
            if (substr($item, -1) === DIRECTORY_SEPARATOR) {
                continue;
            }

            yield ltrim(str_replace($root, "", $item), DIRECTORY_SEPARATOR);
        }
    }

    /**
     * @param string $path
     * @return string
     * @throws \Exception
     */
    public function url(string $path)
    {
        return $this->getBasePath() . $this->getRelativePath($path);
    }

    /**
     * @param string $path
     * @return string
     * @throws \Exception
     */
    public function getFilePath(string $path): string
    {
        return $this->webRoot . $this->getRelativePath($path);
    }

    /**
     * @param $path
     * @return string
     * @throws \Exception
     */
    private function getRelativePath($path): string
    {
        $filePath = $this->buildPath($path);

        if (!file_exists($filePath)) {
            throw new \Exception(sprintf("There is no object with file path (%s)", $path));
        }

        return "/" . ltrim(str_replace([$this->webRoot, "\\"], ["", "/"], $filePath), "/");
    }

    /**
     * @param string $path
     * @return string
     * @throws \Exception
     */
    private function buildPath($path)
    {
        $normalized = DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $relative = substr_count($normalized, DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR)
            + substr_count($normalized, DIRECTORY_SEPARATOR . '.' . DIRECTORY_SEPARATOR);

        if ($relative > 0) {
            throw new \Exception("Relative path is not allowed");
        }

        return $this->path . trim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * @return string
     */
    private function getBasePath()
    {
        $context = $this->urlGenerator->getContext();
        $scheme = $context->getScheme();
        if ($scheme === 'http') {
            $port = $context->getHttpPort();
        } else {
            $port = $context->getHttpsPort();
        }

        return sprintf('%s://%s:%s', $scheme, $context->getHost(), $port);
    }
}
