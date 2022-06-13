<?php

namespace FMT\PublicBundle\ParamConverter\Traits;

trait InheritanceTrait
{
    /**
     * Method checks if target class is equal to supported class
     *
     * @param string $targetClass
     * @param string $supportedClass
     * @return bool
     */
    private function isInstanceOf($targetClass, $supportedClass)
    {
        if (strpos($targetClass, ":")) {
            list($bundle, $class) = explode(":", $targetClass);
            $targetClass = sprintf("FMT\\%s\\Entity\\%s", $bundle, $class);
        }

        return $targetClass === $supportedClass;
    }
}
