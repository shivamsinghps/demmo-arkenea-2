<?php

namespace Tests\FMT\DomainBundle;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractTest
 * @package Tests\FMT\DomainBundle
 */
abstract class AbstractTest extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $client = static::createClient();
        $this->container = $client->getContainer();
    }

    /**
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @param $object
     * @param $property
     * @param $value
     */
    protected function setDisallowedProperty($object, $property, $value)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $prop = $reflection->getProperty($property);
        $prop->setAccessible(true);
        $prop->setValue($object, $value);
    }

    /**
     * @param $class
     * @param $params
     * @return mixed
     */
    protected function createCustomMock($class, $params)
    {
        $entity = $this->createMock($class);
        foreach ($params as $methodName => $value) {
            $setValue = $this->createValue($value);
            $entity->expects($this->any())
                ->method($methodName)
                ->will(
                    $this->returnValue(
                        $setValue
                    )
                );
        }

        return $entity;
    }

    /**
     * @param $class
     * @param $params
     * @return mixed
     */
    protected function createEntity($class, $params)
    {
        $entity = new $class;
        foreach ($params as $methodName => $value) {
            $setValue = $this->createEntityValue($value);
            $entity->{$methodName}($setValue);
        }

        return $entity;
    }

    /**
     * @param string $class
     * @param array $elements
     * @param $type
     * @return ArrayCollection
     */
    protected function createCollection($class, array $elements, $type)
    {
        $collection = new ArrayCollection();
        foreach ($elements as $element) {
            if ($type == 'mock') {
                $setValue = $this->createCustomMock($class, $element);
            } else {
                $setValue = $this->createEntity($class, $element);
            }

            $collection->add($setValue);
        }

        return $collection;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function createValue($value)
    {
        $setValue = null;
        if (is_array($value)) {
            if (!empty($value['isCollection'])) {
                $type = $value['type'] ?? null;
                $setValue = $this->createCollection($value['class'], $value['elements'], $type);
            } else {
                $setValue = $this->createCustomMock($value['class'], $value['methods']);
            }
        } else {
            $setValue = $value;
        }

        return $setValue;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function createEntityValue($value)
    {
        $setValue = null;
        if (is_array($value)) {
            $type = $value['type'] ?? null;

            if (!empty($value['isCollection'])) {
                $setValue = $this->createCollection($value['class'], $value['elements'], $type);
            } elseif ($type == 'mock') {
                $setValue = $this->createCustomMock($value['class'], $value['methods']);
            } else {
                $setValue = $this->createEntity($value['class'], $value['methods']);
            }
        } else {
            $setValue = $value;
        }

        return $setValue;
    }
}
