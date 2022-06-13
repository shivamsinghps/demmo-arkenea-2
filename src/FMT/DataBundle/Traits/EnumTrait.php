<?php

namespace FMT\DataBundle\Traits;

trait EnumTrait
{
    /** @var array */
    private static $allowedStatuses = null;

    /**
     * @param string $prefix
     * @return array
     */
    public static function getConstants($prefix = "STATUS_")
    {
        $prefixLen = strlen($prefix);
        $constants = [];

        $reflection = new \ReflectionClass(__CLASS__);
        foreach ($reflection->getConstants() as $name => $value) {
            if (strlen($name) > $prefixLen && substr($name, 0, $prefixLen) === $prefix) {
                $constants[$name] = $value;
            }
        }

        return $constants;
    }

    /**
     * @return array
     */
    public static function getAllowedStatuses()
    {
        if (is_null(self::$allowedStatuses)) {
            self::$allowedStatuses = self::getConstants("STATUS_");
        }

        return self::$allowedStatuses;
    }
}
