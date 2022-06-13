<?php
/**
 * Author: Anton Orlov
 * Date: 02.04.2018
 * Time: 16:38
 */

namespace FMT\InfrastructureBundle\Service\AmazonS3;

interface StorageInterface
{
    /**
     * @param string|resource|\SplFileInfo $source
     * @param string $destination
     * @return int
     */
    public function upload($source, string $destination);

    /**
     * @param string $source
     * @param null|string|resource|\SplFileInfo $destination
     * @return int
     */
    public function download(string $source, $destination);

    /**
     * @param string $source
     * @return bool
     */
    public function delete(string $source);

    /**
     * @param string $path
     * @return \Generator
     */
    public function glob(string $path);

    /**
     * @param string $path
     * @return string
     */
    public function url(string $path);

    /**
     * @param string $path
     * @return string
     * @throws \Exception
     */
    public function getFilePath(string $path): string;
}
