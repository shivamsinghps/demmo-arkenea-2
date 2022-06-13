<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 14:13
 */

namespace FMT\InfrastructureBundle\Service\Nebook;

/**
 * Class Mapper
 * @package FMT\InfrastructureBundle\Service\Nebook
 */
class Mapper
{
    const DIR_CLIENT_REST_API = "RestApi";
    const DIR_CLIENT_SOAP_API = "SoapApi";
    const MAPPER_NAMESPACE = "Mapper";
    const MAPPER_EXTENSION = ".php";

    /** @var array */
    private static $mappers = null;

    /** @var AbstractMapper[] */
    private static $instances = [];

    /**
     * Method tries to map source data to specific target data type
     *
     * @param mixed $source
     * @param string $targetType
     * @param string $typeClient
     * @return mixed
     */
    public static function map($source, $targetType, $typeClient = self::DIR_CLIENT_REST_API)
    {
        if (is_null($source)) {
            return null;
        }

        if (is_null(self::$mappers)) {
            self::$mappers = self::initMappers(__DIR__ . DIRECTORY_SEPARATOR . $typeClient . DIRECTORY_SEPARATOR . self::MAPPER_NAMESPACE, $typeClient);
        }


        $sourceType = is_object($source) ? get_class($source) : gettype($source);

        if (!isset(self::$mappers[$sourceType]) || !isset(self::$mappers[$sourceType][$targetType])) {
            throw new \InvalidArgumentException(sprintf(
                "There is no mapper to convert data of %s to %s",
                $sourceType,
                $targetType
            ));
        }

        $class = self::$mappers[$sourceType][$targetType];

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }

        return self::$instances[$class]->map($source);
    }

    /**
     * Method iterates items in the list and apply map() method to every item.
     *
     * @param array|\Iterator|null $list
     * @param string $targetType
     * @return array
     */
    public static function mapList($list, $targetType)
    {
        if (is_null($list)) {
            return [];
        }

        $result = [];
        foreach ($list as $key => $item) {
            $result[$key] = self::map($item, $targetType);
        }
        return $result;
    }

    /**
     * Method scans file path with type mappers and collect it into array. Every valid mapper should be extended from
     * AbstractMapper class and implement public method map(). The type of the argument and result of this method should
     * be defined (otherwise, it will not be collected into list of converters). That makes possible to determine what
     * converter could be used for conversion of source data type into target data type.
     *
     * ATTENTION: This method rely on class autoload to load specific classes, so it's important to keep Mapper
     * namespaces organized.
     *
     * @param string $path
     * @param string $typeClient
     * @return array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private static function initMappers($path, $typeClient)
    {
        $result = [];

        if (!is_dir($path)) {
            return $result;
        }

        $extensions = glob($path . DIRECTORY_SEPARATOR . "*" . self::MAPPER_EXTENSION);
        foreach ($extensions as $extension) {
            $class = join("\\", [
                __NAMESPACE__,
                $typeClient,
                self::MAPPER_NAMESPACE,
                basename($extension, self::MAPPER_EXTENSION)
            ]);

            if (!class_exists($class, true)) {
                continue;
            }

            $reflection = new \ReflectionClass($class);
            if (!$reflection->isSubclassOf(AbstractMapper::class) || !$reflection->hasMethod("map")) {
                continue;
            }

            $method = $reflection->getMethod("map");
            $arguments = $method->getParameters();

            if (!$method->isPublic() || count($arguments) != 1) {
                continue;
            }

            $sourceType = (string) $arguments[0]->getType();
            $targetType = (string) $method->getReturnType();

            if (empty($sourceType) || empty($targetType)) {
                continue;
            }

            if (!array_key_exists($sourceType, $result)) {
                $result[$sourceType] = [];
            }

            $result[$sourceType][$targetType] = $class;
        }

        return $result;
    }
}
