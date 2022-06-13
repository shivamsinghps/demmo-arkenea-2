<?php

namespace Tests\FMT\DomainBundle\Service\Mapper\Campaign\Book;

use FMT\DomainBundle\Service\Mapper\Campaign\Book\SectionMapper;
use FMT\DomainBundle\Type\Campaign\Book\Section;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Section as NebookSection;
use Tests\FMT\DomainBundle\AbstractTest;

/**
 * Class SectionMapperTest
 * @package Tests\FMT\DomainBundle\Service\Mapper\Campaign\Book
 */
class SectionMapperTest extends AbstractTest
{
    private $service;

    /**
     *  Set Up
     */
    public function setUp()
    {
        parent::setUp();
        $this->service = $this->container->get(SectionMapper::class);
    }

    /**
     * @param $params
     * @param $expectedParams
     * @dataProvider dataProvider
     */
    public function testMap($params, $expectedParams)
    {
        $entity = $this->createCustomMock($params['class'], $params['methods']);

        /** @var Section $expectedResult */
        $expectedResult = $this->createCustomMock($expectedParams['class'], $expectedParams['methods']);

        /** @var Section $result */
        $result = $this->invokeMethod($this->service, 'map', [&$entity]);

        $this->assertEquals($expectedResult->getId(), $result->getId());
        $this->assertEquals($expectedResult->getName(), $result->getName());
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $id = 1;

        $result = [];

        // 1.
        $name = 'Test Name';
        $instructorName = 'Test Instructor Name';

        $result[] = [
            'params' => [
                'class' => NebookSection::class,
                'methods' => [
                    'getId' => $id,
                    'getName' => $name,
                    'getInstructorName' => $instructorName,
                ],
            ],
            'expected' => [
                'class' => Section::class,
                'methods' => [
                    'getId' => $id,
                    'getName' => sprintf('%s: %s', $instructorName, $name),
                ],
            ],
        ];


        // 2.
        $name = '';
        $instructorName = 'Test Instructor Name';

        $result[] = [
            'params' => [
                'class' => NebookSection::class,
                'methods' => [
                    'getId' => $id,
                    'getName' => $name,
                    'getInstructorName' => $instructorName,
                ],
            ],
            'expected' => [
                'class' => Section::class,
                'methods' => [
                    'getId' => $id,
                    'getName' => $instructorName,
                ],
            ],
        ];


        // 3.
        $name = '';
        $instructorName = '';

        $result[] = [
            'params' => [
                'class' => NebookSection::class,
                'methods' => [
                    'getId' => $id,
                    'getName' => $name,
                    'getInstructorName' => $instructorName,
                ],
            ],
            'expected' => [
                'class' => Section::class,
                'methods' => [
                    'getId' => $id,
                    'getName' => $instructorName,
                ],
            ],
        ];

        return $result;
    }
}
