<?php
/**
 * Author: Anton Orlov
 * Date: 04.04.2018
 * Time: 16:29
 */

namespace FMT\InfrastructureBundle\Helper;

use GuzzleHttp\Psr7\Stream;

class FileHelper
{
    /** @var bool */
    public static $isOpened;

    /** @var array */
    private static $mapping = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true
        ]
    ];

    /**
     * @param string|resource|\SplFileInfo $source
     * @param string|resource|\SplFileInfo $destination
     * @return int
     */
    public static function copy($source, $destination)
    {
        $sourceStream = self::open($source, "rb");
        $isOpenedSourceStream = self::$isOpened;

        $destinationStream = self::open($destination, "wb");
        $isOpenedDestinationStream = self::$isOpened;

        $result = stream_copy_to_stream($sourceStream, $destinationStream);

        if ($isOpenedSourceStream) {
            fclose($sourceStream);
        }

        if ($isOpenedDestinationStream) {
            fclose($destinationStream);
        }

        return $result;
    }

    /**
     * @param string|resource|\SplFileInfo $file
     * @param string $mode
     * @return resource
     */
    public static function open($file, $mode = "rb")
    {
        self::$isOpened = false;
        $handler = array_keys(array_filter([
            "openFromFileInfo" => $file instanceof \SplFileInfo,
            "openFromPsrStream" => $file instanceof Stream,
            "openFromStream" => is_resource($file),
            "openFromPath" => true
        ]));

        return call_user_func(__CLASS__ . "::" . array_shift($handler), $file, $mode);
    }

    /**
     * @param string $path
     * @param $mode
     * @return resource
     * @throws \RuntimeException
     */
    protected static function openFromPath($path, $mode)
    {
        if (!($result = fopen($path, $mode))) {
            throw new \RuntimeException("Unable to open file to read");
        }

        self::$isOpened = true;

        return $result;
    }

    /**
     * @param resource $stream
     * @param $mode
     * @return resource
     * @throws \RuntimeException
     */
    protected static function openFromStream($stream, $mode)
    {
        $meta = stream_get_meta_data($stream);

        if (!self::isSameMode($meta["mode"], $mode)) {
            fflush($stream);
            $stream = self::openFromPath($meta["uri"], $mode);
        } elseif ($meta["seekable"] && !rewind($stream)) {
            throw new \RuntimeException("Could not rewind provided data source");
        }

        return $stream;
    }

    /**
     * ATTENTION: This method based on expectation that Stream class contains private property with stream object.
     *
     * @param Stream $stream
     * @param string $mode
     * @return resource
     */
    protected static function openFromPsrStream($stream, $mode)
    {
        $resource = null;
        $reflection = new \ReflectionClass($stream);

        if ($reflection->hasProperty("stream")) {
            $properties = [$reflection->getProperty("stream")];
        } else {
            $properties = $reflection->getProperties();
        }

        foreach ($properties as $property) {
            $isAccessible = $property->isPublic();
            $property->setAccessible(true);
            $value = $property->getValue($stream);
            $property->setAccessible($isAccessible);

            if (is_resource($value)) {
                $type = get_resource_type($value);
                if ($type == "file" || $type == "stream") {
                    $resource = $value;
                    break;
                }
            }
        }

        if (is_null($resource)) {
            throw new \RuntimeException("Provided stream object does not contain valid stream property");
        }

        return self::openFromStream($resource, $mode);
    }

    /**
     * @param \SplFileInfo $info
     * @param $mode
     * @return resource
     */
    protected static function openFromFileInfo($info, $mode)
    {
        return self::openFromPath($info->getRealPath(), $mode);
    }

    /**
     * @param string $mode
     * @return string
     */
    private static function normalizeMode($mode)
    {
        static $order = ["a", "c", "r", "w", "x", "+", "b", "t"];
        $result = str_split(strtolower($mode));
        usort($result, function ($itemA, $itemB) use ($order) {
            return array_search($itemA, $order) <=> array_search($itemB, $order);
        });
        return join("", $result);
    }

    /**
     * @param string $currentMode
     * @param string $requiredMode
     * @return bool
     */
    private static function isSameMode($currentMode, $requiredMode)
    {
        $currentMode = self::normalizeMode($currentMode);
        $requiredMode = self::normalizeMode($requiredMode);

        $current = array_sum(array_keys(array_filter([
            1 => isset(self::$mapping["read"][$currentMode]),
            2 => isset(self::$mapping["write"][$currentMode])
        ])));
        $required = array_sum(array_keys(array_filter([
            1 => isset(self::$mapping["read"][$requiredMode]),
            2 => isset(self::$mapping["write"][$requiredMode])
        ])));

        return (($current & $required) == $required);
    }
}
