<?php

namespace Tests\FMT\DomainBundle\Service\Mapper\Campaign\Book;

use FMT\DomainBundle\Service\Mapper\Campaign\Book\ProductMapper;
use FMT\DomainBundle\Type\Campaign\Book\Product;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Product as NebookProduct;
use Tests\FMT\DomainBundle\AbstractTest;

/**
 * Class ProductMapperTest
 * @package Tests\FMT\DomainBundle\Service\Mapper\Campaign\Book
 */
class ProductMapperTest extends AbstractTest
{
    private $service;

    /**
     *  Set Up
     */
    public function setUp()
    {
        parent::setUp();
        $this->service = $this->container->get(ProductMapper::class);
    }

    /**
     * @param $params
     * @param $expectedParams
     * @dataProvider dataProvider
     */
    public function testMap($params, $expectedParams)
    {
        $entity = $this->createCustomMock($params['class'], $params['methods']);

        /** @var Product $expectedResult */
        $expectedResult = $this->createCustomMock($expectedParams['class'], $expectedParams['methods']);

        /** @var Product $result */
        $result = $this->invokeMethod($this->service, 'map', [&$entity]);

        $this->assertEquals($expectedResult->getSku(), $result->getSku());
        $this->assertEquals($expectedResult->getState(), $result->getState());
        $this->assertEquals($expectedResult->getPrice(), $result->getPrice());
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $sku = 'Test Sku';
        $price = 123456;

        $result = [];

        // 1.
        $state = 'TEST STATE';

        $result[] = [
            'params' => [
                'class' => NebookProduct::class,
                'methods' => [
                    'getSku' => $sku,
                    'getState' => $state,
                    'getPrice' => $price,
                ],
            ],
            'expected' => [
                'class' => Product::class,
                'methods' => [
                    'getSku' => $sku,
                    'getState' => 'Test state',
                    'getPrice' => $price,
                ],
            ],
        ];

        // 2.
        $state = 'test state';

        $result[] = [
            'params' => [
                'class' => NebookProduct::class,
                'methods' => [
                    'getSku' => $sku,
                    'getState' => $state,
                    'getPrice' => $price,
                ],
            ],
            'expected' => [
                'class' => Product::class,
                'methods' => [
                    'getSku' => $sku,
                    'getState' => 'Test state',
                    'getPrice' => $price,
                ],
            ],
        ];

        return $result;
    }
}
