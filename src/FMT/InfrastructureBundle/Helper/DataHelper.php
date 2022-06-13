<?php
/**
 * Author: Anton Orlov
 * Date: 27.02.2018
 * Time: 18:00
 */

namespace FMT\InfrastructureBundle\Helper;

/**
 * Class DataHelper
 * @package FMT\InfrastructureBundle\Helper
 *
 * TODO: Getters and setters for different types should be moved out of class object into specific classes
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class DataHelper
{
    const PATH_DELIMITER = ".";

    /** @var object|array */
    private $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name);
    }


    public function set($path, $value)
    {
        self::setValue($this->context, $path, $value);
    }

    /**
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function get($path, $default = null)
    {
        return self::getValue($this->context, $path, $default);
    }

    /**
     * @param string $path
     * @param int|null $default
     * @return int
     */
    public function getInt($path, $default = null)
    {
        $result = self::getValue($this->context, $path);

        if (is_null($result)) {
            $result = is_int($default) ? $default : null;
        } else {
            $result = (int) $result;
        }

        return $result;
    }

    /**
     * @param string $path
     * @param float|null $default
     * @return float
     */
    public function getFloat($path, $default = null)
    {
        $result = self::getValue($this->context, $path);

        if (is_null($result)) {
            $result = is_float($default) ? $default : null;
        } else {
            $result = (double) $result;
        }

        return $result;
    }

    /**
     * @param string $path
     * @param bool|null $default
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getBool($path, $default = null)
    {
        $result = self::getValue($this->context, $path);

        if (is_null($result)) {
            $result = is_bool($default) ? $default : null;
        } else {
            $result = (bool) $result;
        }

        return $result;
    }

    /**
     * @param string $path
     * @param string|null $default
     * @return string
     */
    public function getString($path, $default = null)
    {
        $result = self::getValue($this->context, $path);

        if (is_null($result)) {
            $result = is_string($default) ? $default : null;
        } else {
            $result = (string) $result;
        }

        return $result;
    }

    /**
     * @param string $path
     * @param \DateTime|null $default
     * @return \DateTime
     */
    public function getDate($path, $default = null)
    {
        $result = self::getValue($this->context, $path);

        if (!($result instanceof \DateTime)) {
            $result = DateHelper::guess($result);

            if (!($result instanceof \DateTime)) {
                $result = $default instanceof \DateTime ? $default : null;
            }
        }

        return $result;
    }

    /**
     * @param array|object $src
     * @param array|object $dst
     * @param array $mapping
     */
    public static function map($src, $dst, $mapping)
    {
        static $typeToMethod = [
            "int"      => "getInt",
            "integer"  => "getInt",
            "float"    => "getFloat",
            "double"   => "getFloat",
            "decimal"  => "getFloat",
            "bool"     => "getBool",
            "boolean"  => "getBool",
            "string"   => "getString",
            "text"     => "getString",
            "date"     => "getDate",
            "datetime" => "getDate",
            "time"     => "getDate"
        ];
        $class = self::class;
        $wrapper = new $class($src);

        foreach ($mapping as $srcPath => $dstPath) {
            $method = "get";
            if (($start = strpos($srcPath, "(")) !== false && ($end = strpos($srcPath, ")")) !== false) {
                $type = trim(strtolower(substr($srcPath, 0, $start)));
                $srcPath = substr($srcPath, $start + 1, $end - $start);
                if (array_key_exists($type, $typeToMethod)) {
                    $method = $typeToMethod[$type];
                }
            }
            self::setValue($dst, $dstPath, call_user_func([$wrapper, $method], $srcPath));
        }
    }

    /**
     * @param object|array $data
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($data, $path, $default = null)
    {
        $result = $data;
        $components = explode(self::PATH_DELIMITER, $path);
        try {
            while (!empty($components)) {
                $component = array_shift($components);
                $result = self::getFrom($result, $component);
            }
        } catch (\InvalidArgumentException $e) {
            $result = $default;
        }

        return $result;
    }

    /**
     * @param object|array $data
     * @param string $path
     * @param mixed $value
     */
    public static function setValue($data, $path, $value)
    {
        $target = $data;
        $components = explode(self::PATH_DELIMITER, $path);
        $key = array_pop($components);
        try {
            while (!empty($components)) {
                $component = array_shift($components);
                $target = self::getFrom($target, $component);
            }

            self::setTo($target, $key, $value);
        } catch (\InvalidArgumentException $e) {
            return;
        }
    }

    /**
     * @param object|array $data
     * @param string $key
     * @param mixed $value
     */
    protected static function setTo($data, $key, $value)
    {
        $handler = array_keys(array_filter([
            "setToArray" => is_array($data) || $data instanceof \ArrayAccess,
            "setToObject" => is_object($data)
        ]));

        if (empty($handler)) {
            throw new \InvalidArgumentException("Unsupported object type");
        }

        call_user_func([__CLASS__, array_shift($handler)], $data, $key, $value);
    }

    /**
     * @param object $object
     * @param string $key
     * @param mixed $value
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected static function setToObject($object, $key, $value)
    {
        $reflection = new \ReflectionObject($object);

        $property = lcfirst($key);
        $method = "set" . ucfirst($key);
        if (strpos($key, "_") !== false) {
            $name = explode("_", strtolower($key));
            $name = array_map("ucfirst", $name);
            $method = "set" . join("", $name);
            $property = lcfirst(join("", $name));
        }

        $propertyReflection = $reflection->hasProperty($property) ? $reflection->getProperty($property) : null;
        $methodReflection = $reflection->hasMethod($method) ? $reflection->getMethod($method) : null;

        if (!is_null($propertyReflection) && $propertyReflection->isPublic()) {
            $propertyReflection->setValue($object, $value);
            return;
        }

        if (!is_null($methodReflection) && $methodReflection->isPublic()) {
            $methodReflection->invoke($object, $value);
            return;
        }

        if ($reflection->hasMethod("__set")) {
            $object->{$property} = $value;
            return;
        }

        if (!is_null($propertyReflection) && !$propertyReflection->isPublic()) {
            $propertyReflection->setAccessible(true);
            $propertyReflection->setValue($object, $value);
            $propertyReflection->setAccessible(false);
            return;
        }
    }

    /**
     * @param array $array
     * @param string $key
     * @param mixed $value
     */
    protected static function setToArray($array, $key, $value)
    {
        if (!empty($array) && ArrayHelper::isArray($array)) {
            $key = (int) $key;
        }

        $array[$key] = $value;
    }

    /**
     * @param object|array $data
     * @param string $key
     * @return mixed
     */
    protected static function getFrom($data, $key)
    {
        $handler = array_keys(array_filter([
            "getFromArray" => is_array($data) || $data instanceof \ArrayAccess,
            "getFromObject" => is_object($data)
        ]));

        if (empty($handler)) {
            throw new \InvalidArgumentException("Unsupported object type");
        }

        return call_user_func([__CLASS__, array_shift($handler)], $data, $key);
    }

    /**
     * Method looks for path element of object
     *
     * @param object $object
     * @param string $key
     * @return mixed
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected static function getFromObject($object, $key)
    {
        $reflection = new \ReflectionObject($object);

        $property = lcfirst($key);
        $method = ucfirst($key);
        if (strpos($key, "_") !== false) {
            $name = explode("_", strtolower($key));
            $name = array_map("ucfirst", $name);
            $method = join("", $name);
            $property = lcfirst($method);
        }

        if ($reflection->hasProperty($property) && $reflection->getProperty($property)->isPublic()) {
            return $object->{$property};
        }

        $methods = array_filter(array_map(function ($prefix) use ($reflection, $method) {
            $name = sprintf("%s%s", $prefix, $method);

            return $reflection->hasMethod($name) && $reflection->getMethod($name)->isPublic() ? $name : false;
        }, ["get", "has", "is"]));

        if (!empty($methods)) {
            return call_user_func([$object, array_shift($methods)]);
        }

        if (method_exists($object, "__get")) {
            return $object->{$property};
        }

        if (method_exists($object, "toArray")) {
            return self::getFromArray($object->toArray(), $key);
        }

        throw new \InvalidArgumentException(sprintf("Key (%s) could not be found for this object", $key));
    }

    /**
     * Method looks for path element of array
     *
     * @param array $array
     * @param string $key
     * @return mixed
     */
    protected static function getFromArray($array, $key)
    {
        $result = null;
        if (ArrayHelper::isArray($array)) {
            $key = (int) $key;
        }

        if (array_key_exists($key, $array)) {
            $result = $array[$key];
        }

        return $result;
    }
}
