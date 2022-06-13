<?php

namespace Tests\FMT\InfrastructureBundle;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractTest
 * @package Tests\FMT\InfrastructureBundle
 */
abstract class AbstractTest extends KernelTestCase
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
        parent::setUp();
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
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
     * @param $propery
     * @param $value
     */
    protected function setDisallowedProperty($object, $propery, $value)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $prop = $reflection->getProperty($propery);
        $prop->setAccessible(true);
        $prop->setValue($object, $value);
    }
}
