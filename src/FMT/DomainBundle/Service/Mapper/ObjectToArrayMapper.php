<?php

/**
 * Created by Karina Kalina
 * Date: 24.04.18
 * Time: 14:57
 */

namespace FMT\DomainBundle\Service\Mapper;

use Doctrine\Common\Collections\Collection;
use FMT\InfrastructureBundle\Helper\DateHelper;

class ObjectToArrayMapper
{
    /**
     * @param $source
     * @param int $maxLevel
     * @param int $level
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function map($source, $maxLevel = 2, $level = 1) : array
    {
        if (!is_object($source) || $level > $maxLevel) {
            return [];
        }

        $result = [];
        $reflection = new \ReflectionClass($source);

        foreach ($reflection->getMethods() as $method) {
            $methodName = $method->getName();

            if (preg_match('/^get|is/', $methodName) &&
                $method->getNumberOfParameters() == 0 &&
                $methodName != 'getAllowedStatuses') {
                $value = $source->$methodName();

                if ($value instanceof \DateTime) {
                    $value = $value->format(DateHelper::PHP_STANDARD_FORMAT);
                } elseif ($value instanceof Collection) {
                    $arr = [];
                    foreach ($value as $item) {
                        $arr[] = self::map($item, $maxLevel, $level + 1);
                    }

                    $value = $arr;
                } elseif (is_object($value) && $level + 1 > $maxLevel) {
                    continue;
                } elseif (is_object($value)) {
                    $value = self::map($value, $maxLevel, $level + 1);
                }

                preg_match_all('/[A-Z][^A-Z]*?/Us', $methodName, $matches);

                $result[strtolower(implode('-', $matches[0]))] = $value;
            }
        }

        return $result;
    }
}
