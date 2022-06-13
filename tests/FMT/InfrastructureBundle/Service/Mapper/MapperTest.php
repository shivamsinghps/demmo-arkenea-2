<?php

namespace Tests\FMT\InfrastructureBundle\Service\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Mapper\Mapper;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\FMT\InfrastructureBundle\Service\Mapper\TestMappers\ArrayMapper;
use Tests\FMT\InfrastructureBundle\Service\Mapper\TestMappers\IntegerMapper;

class MapperTest extends TestCase
{
    public function testErrorMap(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mapper = $this->getMapper();
        $mapper->map('Some value', 'Some type');
    }

    /**
     * @param        $variable
     * @param string $mapToClass
     * @param        $expected
     * @param array  $mappers
     *
     * @dataProvider mapProvider
     */
    public function testMap($variable, string $mapToClass, $expected, array $mappers): void
    {
        $mapper = $this->getMapper($mappers);

        $this->assertSame($expected, $mapper->map($variable, $mapToClass), true);
    }

    /**
     * @param        $variable
     * @param string $mapTo
     * @param        $expected
     * @param array  $mappers
     *
     * @depends testMap
     * @dataProvider mapProvider
     */
    public function testMapList($variable, string $mapTo, $expected, array $mappers): void
    {
        $mapper = $this->getMapper($mappers);

        $arrayOfExpected = array_fill(0, 8, $expected);
        $arrayOfVariables = array_fill(0, 8, $variable);

        $this->assertSame($arrayOfExpected, $mapper->mapList($arrayOfVariables, $mapTo), true);
    }

    /**
     * @return array
     */
    public function mapProvider(): array
    {
        return [
            [
                'variable' => 123,
                'mapTo' => 'string',
                'expected' => '123',
                'mappers' => [
                    'integer' => [
                        'string' => [IntegerMapper::class, 'mapToString'],
                    ],
                ],
            ],
            [
                'variable' => [
                    1,
                    [2],
                    [3, [4]],
                    5,
                    [6, [7, [8], 9], 10],
                ],
                'mapToClass' => 'integer',
                'expected' => 55,
                'mappers' => [
                    'array' => [
                        'integer' => [ArrayMapper::class, 'mapToSum'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array $mappers
     *
     * @return Mapper
     */
    private function getMapper(array $mappers = []): Mapper
    {
        $mapper = new Mapper();
        $mapper->setMappers($mappers);

        return $mapper;
    }

    /**
     * @return AbstractMapper|MockObject
     */
    private function getSomeMapper(): AbstractMapper
    {
        return $this->getMockBuilder(IntegerMapper::class)
            ->setConstructorArgs([$this->createMock(Mapper::class)])
            ->getMock();
    }
}
