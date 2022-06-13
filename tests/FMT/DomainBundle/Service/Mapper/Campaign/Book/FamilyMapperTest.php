<?php

namespace Tests\FMT\DomainBundle\Service\Mapper\Campaign\Book;

use FMT\DomainBundle\Service\Mapper\Campaign\Book\FamilyMapper;
use FMT\DomainBundle\Type\Campaign\Book\Product;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\BookInfo as NebookBookInfo;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ProductFamily as NebookFamily;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Product as NebookProduct;
use Tests\FMT\DomainBundle\AbstractTest;

/**
 * Class FamilyMapperTest
 * @package Tests\FMT\DomainBundle\Service\Mapper\Campaign\Book
 */
class FamilyMapperTest extends AbstractTest
{
    private $service;

    /**
     *  Set Up
     */
    public function setUp()
    {
        parent::setUp();
        $this->service = $this->container->get(FamilyMapper::class);
    }

    /**
     * @param $params
     * @param $expectedParams
     * @dataProvider dataProvider
     */
    public function testMap($params, $expectedParams)
    {
        $entity = $this->createCustomMock($params['class'], $params['methods']);

        $expectedResults = [];
        foreach ($expectedParams as $expectedParam) {
            $expectedResults[] = $this->createCustomMock($expectedParam['class'], $expectedParam['methods']);
        }

        /** @var Product[] $result */
        $results = $this->invokeMethod($this->service, 'map', [&$entity]);

        $this->assertEquals(count($expectedResults), count($results));

        foreach ($expectedResults as $key => $expectedResult) {
            /** @var Product $result */
            $result = $results[$key];
            $this->assertEquals($expectedResult->getFamilyId(), $result->getFamilyId());
            $this->assertEquals($expectedResult->getName(), $result->getName());
            $this->assertEquals($expectedResult->getAuthor(), $result->getAuthor());
            $this->assertEquals($expectedResult->getIsbn(), $result->getIsbn());
        }
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $familyId = 1;
        $name = 'Test Name';
        $auth = 'Test Auth';
        $isbn = 'Test Isbn';

        $result = [];

        // 1.
        $result[] = [
            'params' => [
                'class' => NebookFamily::class,
                'methods' => [
                    'getId' => $familyId,
                    'getName' => $name,
                    'getInfo' => [
                        'class' => NebookBookInfo::class,
                        'methods' => [
                            'getAuthor' => $auth,
                            'getIsbn' => $isbn,
                        ],
                    ],
                    'getProducts' => [
                        'isCollection' => true,
                        'class' => NebookProduct::class,
                        'elements' => [
                            [],
                            [],
                            [],
                        ]
                    ]
                ],
            ],
            'expected' => [
                [
                    'class' => Product::class,
                    'methods' => [
                        'getFamilyId' => $familyId,
                        'getName' => $name,
                        'getAuthor' => $auth,
                        'getIsbn' => $isbn,
                    ],
                ],
                [
                    'class' => Product::class,
                    'methods' => [
                        'getFamilyId' => $familyId,
                        'getName' => $name,
                        'getAuthor' => $auth,
                        'getIsbn' => $isbn,
                    ],
                ],
                [
                    'class' => Product::class,
                    'methods' => [
                        'getFamilyId' => $familyId,
                        'getName' => $name,
                        'getAuthor' => $auth,
                        'getIsbn' => $isbn,
                    ],
                ],
            ],
        ];


        // 2.
        $result[] = [
            'params' => [
                'class' => NebookFamily::class,
                'methods' => [
                    'getId' => $familyId,
                    'getName' => $name,
                    'getInfo' => [
                        'class' => NebookBookInfo::class,
                        'methods' => [
                            'getAuthor' => $auth,
                            'getIsbn' => $isbn,
                        ],
                    ],
                    'getProducts' => [
                        'isCollection' => true,
                        'class' => NebookProduct::class,
                        'elements' => [
                            [],
                        ]
                    ]
                ],
            ],
            'expected' => [
                [
                    'class' => Product::class,
                    'methods' => [
                        'getFamilyId' => $familyId,
                        'getName' => $name,
                        'getAuthor' => $auth,
                        'getIsbn' => $isbn,
                    ],
                ],
            ],
        ];

        return $result;
    }
}
