<?php

namespace Tests\FMT\InfrastructureBundle\Service\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Mapper\Mapper;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Class AbstractMapperTest
 */
class AbstractMapperTest extends TestCase
{
    private function callProtectedMethod($object, string $method, array $args)
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $reflectionMethod = $reflectionClass->getMethod($method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($object, $args);
    }

    public function testToIntPrice(): void
    {
        $abstractMapper = $this->getAbstractMapper();

        $this->assertEquals(1234, $this->callProtectedMethod($abstractMapper, 'toIntPrice', [12.34]));
        $this->assertEquals(1200, $this->callProtectedMethod($abstractMapper, 'toIntPrice', [12]));
        $this->assertEquals(1212, $this->callProtectedMethod($abstractMapper, 'toIntPrice', [12.123]));
        $this->assertEquals(1213, $this->callProtectedMethod($abstractMapper, 'toIntPrice', [12.125]));
        $this->assertEquals(1213, $this->callProtectedMethod($abstractMapper, 'toIntPrice', [12.126]));
        $this->assertNull($this->callProtectedMethod($abstractMapper, 'toIntPrice', [null]));
    }

    public function testFromIntPrice(): void
    {
        $abstractMapper = $this->getAbstractMapper();

        $this->assertEquals(12.34, $this->callProtectedMethod($abstractMapper, 'fromIntPrice', [1234]));
        $this->assertEquals(12.00, $this->callProtectedMethod($abstractMapper, 'fromIntPrice', [1200]));
        $this->assertEquals(12.12, $this->callProtectedMethod($abstractMapper, 'fromIntPrice', [1212]));
        $this->assertEquals(99.99, $this->callProtectedMethod($abstractMapper, 'fromIntPrice', [9999]));
        $this->assertNull($this->callProtectedMethod($abstractMapper, 'fromIntPrice', [null]));
    }

    /**
     * @param array $expect
     * @param array $data
     *
     * @dataProvider nvpToDictProvider
     */
    public function testMapNvpToDict(array $expect, array $data): void
    {
        $abstractMapper = $this->getAbstractMapper();

        $this->assertArraySubset($expect, $this->callProtectedMethod($abstractMapper, 'mapNvpToDict', [$data]), true);
    }

    /**
     * @return array
     */
    public function nvpToDictProvider(): array
    {
        return [
            [
                'expect' => [],
                'data' => [],
            ],
            [
                'expect' => ['someField' => '123', 'someEmptyField' => null],
                'data' => [
                    ['Name' => 'someField', 'Value' => 1234],
                    ['Something other'],
                    ['Name' => 'someEmptyField'],
                    ['Name' => 'someField', 'Value' => 123],
                ],
            ],
            [
                'expect' => ['someField' => '123', 'someFloatField' => '22.12', 'someEmptyField' => null],
                'data' => [
                    ['Name' => 'someField', 'Value' => 123, 'SortOrder' => 1],
                    ['Name' => 'someFloatField', 'Value' => 12.22, 'SortOrder' => 0],
                    ['Name' => 'someFloatField', 'Value' => 22.12],
                    ['Something other'],
                    ['Name' => 'someEmptyField'],
                    ['Name' => 'someField', 'Value' => 1234],
                ],
            ],
        ];
    }

    /**
     * @return AbstractMapper
     */
    private function getAbstractMapper(): AbstractMapper
    {
        return $this->getMockBuilder(AbstractMapper::class)
            ->setConstructorArgs([$this->createMock(Mapper::class)])
            ->getMockForAbstractClass();
    }
}
