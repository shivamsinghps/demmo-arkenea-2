<?php

namespace FMT\InfrastructureBundle\Service\Mapper;

use InvalidArgumentException;

/**
 * Class Mapper
 * @package FMT\InfrastructureBundle\Service\Mapper
 */
class Mapper
{
    /** @var array */
    private $mappers = [];

    /** @var AbstractMapper[] */
    private $instances = [];

    /**
     * @param array $mappers
     */
    public function setMappers(array $mappers): void
    {
        $this->mappers = $mappers;
    }

    /**
     * Method tries to map source data to specific target data type
     *
     * @param mixed  $source
     * @param string $targetType
     *
     * @return mixed
     */
    public function map($source, string $targetType)
    {
        if (is_null($source)) {
            return null;
        }

        $sourceType = is_object($source) ? get_class($source) : gettype($source);

        if (!isset($this->mappers[$sourceType]) || !isset($this->mappers[$sourceType][$targetType])) {
            throw new InvalidArgumentException(sprintf(
                "There is no mapper to convert data of %s to %s",
                $sourceType,
                $targetType
            ));
        }

        return $this->getMapperInstance($sourceType, $targetType)($source);
    }

    /**
     * Method iterates items in the list and apply map() method to every item.
     *
     * @param array|\Iterator $list
     * @param string $targetType
     * @return array
     */
    public function mapList($list, $targetType)
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
     * @param string $sourceType
     * @param string $targetType
     *
     * @return Callable
     */
    private function getMapperInstance(string $sourceType, string $targetType): Callable
    {
        $mapperClass = $this->mappers[$sourceType][$targetType][0];
        $methodName = $this->mappers[$sourceType][$targetType][1];

        if (!isset($this->instances[$mapperClass])) {
            $this->instances[$mapperClass] = new $mapperClass($this);
        }

        return [$this->instances[$mapperClass], $methodName];
    }
}
