<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 11:11
 */

namespace FMT\InfrastructureBundle\Helper;

class ArrayHelper
{
    /**
     * Method tries to detect if provided object could be used as array
     *
     * @param mixed $object
     * @return bool
     */
    public static function isArray($object)
    {
        if ($object instanceof \Iterator) {
            return true;
        } elseif (!is_array($object)) {
            return false;
        }

        return array_keys($object) === range(0, count($object) - 1);
    }

    /**
     * Method tries to detect if provided object is associated array
     *
     * @param mixed $object
     * @return bool
     */
    public static function isDict($object)
    {
        if ($object instanceof \ArrayAccess) {
            return true;
        } elseif (!is_array($object)) {
            return false;
        }

        return !self::isArray($object);
    }

    /**
     * Method converts object to array
     *
     * @param mixed $object
     * @return array
     */
    public static function objectToArray($object)
    {
        foreach ($object as $k => $obj) {
            if (is_object($obj)) {
                $object->$k = self::objectToArray($obj);
            } else {
                $object->$k = $obj;
            }
        }
        return (array) $object;
    }
}
