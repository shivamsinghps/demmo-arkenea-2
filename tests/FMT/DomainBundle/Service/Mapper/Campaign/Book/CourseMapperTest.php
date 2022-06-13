<?php

namespace Tests\FMT\DomainBundle\Service\Mapper\Campaign\Book;

use FMT\DomainBundle\Service\Mapper\Campaign\Book\CourseMapper;
use FMT\DomainBundle\Type\Campaign\Book\Course;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Course as NebookCourse;
use Tests\FMT\DomainBundle\AbstractTest;

/**
 * Class CourseMapperTest
 * @package Tests\FMT\DomainBundle\Service\Mapper\Campaign\Book
 */
class CourseMapperTest extends AbstractTest
{
    private $service;

    /**
     *  Set Up
     */
    public function setUp()
    {
        parent::setUp();
        $this->service = $this->container->get(CourseMapper::class);
    }

    /**
     * @param $params
     * @param $expectedParams
     * @dataProvider dataProvider
     */
    public function testMap($params, $expectedParams)
    {
        $entity = $this->createCustomMock($params['class'], $params['methods']);

        /** @var Course $expectedResult */
        $expectedResult = $this->createCustomMock($expectedParams['class'], $expectedParams['methods']);

        /** @var Course $result */
        $result = $this->invokeMethod($this->service, 'map', [&$entity]);

        $this->assertEquals($expectedResult->getId(), $result->getId());
        $this->assertEquals($expectedResult->getName(), $result->getName());
        $this->assertEquals($expectedResult->getRealName(), $result->getRealName());
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $id = 1;
        $termId = 2;
        $backofficeId = 3;

        $result = [];

        // 1.
        $name = 'Test Name';
        $description = 'Test Description';

        $result[] = [
            'params' => [
                'class' => NebookCourse::class,
                'methods' => [
                    'getId' => $id,
                    'getTermId' => $termId,
                    'getBackofficeId' => $backofficeId,
                    'getName' => $name,
                    'getDescription' => $description,
                ],
            ],
            'expected' => [
                'class' => Course::class,
                'methods' => [
                    'getId' => $id,
                    'getName' => $description,
                    'getRealName' => $name,
                ],
            ],
        ];


        // 2.
        $name = 'Test Name';
        $description = '';

        $result[] = [
            'params' => [
                'class' => NebookCourse::class,
                'methods' => [
                    'getId' => $id,
                    'getTermId' => $termId,
                    'getBackofficeId' => $backofficeId,
                    'getName' => $name,
                    'getDescription' => $description,
                ],
            ],
            'expected' => [
                'class' => Course::class,
                'methods' => [
                    'getId' => $id,
                    'getName' => $name,
                    'getRealName' => $name,
                ],
            ],
        ];


        // 3.
        $name = '';
        $description = '';

        $result[] = [
            'params' => [
                'class' => NebookCourse::class,
                'methods' => [
                    'getId' => $id,
                    'getTermId' => $termId,
                    'getBackofficeId' => $backofficeId,
                    'getName' => $name,
                    'getDescription' => $description,
                ],
            ],
            'expected' => [
                'class' => Course::class,
                'methods' => [
                    'getId' => $id,
                    'getName' => Course::UNKNOWN_NAME,
                    'getRealName' => $name,
                ],
            ],
        ];

        return $result;
    }
}
