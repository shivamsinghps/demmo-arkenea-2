<?php

namespace FMT\InfrastructureBundle\Helper;

/**
 * Class StringHelper
 */
class StringHelper
{
    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function strStartsWith(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) === 0;
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function strEndsWith(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) === strlen($needle);
    }
}
