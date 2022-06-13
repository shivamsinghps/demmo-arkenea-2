<?php

namespace Tests\FMT\DomainBundle\Service\Mapper;

use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\CampaignBook;
use FMT\DomainBundle\Service\Mapper\ObjectToArrayMapper;
use FMT\InfrastructureBundle\Helper\DateHelper;
use Tests\FMT\DomainBundle\AbstractTest;

/**
 * Class ObjectToArrayMapperTest
 * @package Tests\FMT\DomainBundle
 */
class ObjectToArrayMapperTest extends AbstractTest
{
    /**
     * @dataProvider dataProvider
     * @param $params
     * @param $maxLevel
     * @param $expected
     */
    public function testMap($params, $maxLevel, $expected)
    {
        $book = $this->createEntity($params['class'], $params['methods']);
        $result = ObjectToArrayMapper::map($book, $maxLevel);

        $this->assertEquals($result, $expected);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $result = [];

        $familyId = 1;
        $sku = 123;
        $title = 'Test title';
        $isbn = 1234567890;
        $price = 100;

        $startDate = (new \DateTime())->modify('+1 month');
        $endDate = (new \DateTime())->modify('+2 month');
        $estimatedCost = 100;


        $paramsCampaign = [
            'class' => Campaign::class,
            'methods' => [
                'setStartDate' => $startDate,
                'setEndDate' => $endDate,
                'setEstimatedCost' => $estimatedCost,
            ],
        ];

        $paramsBook = [
            'class' => CampaignBook::class,
            'methods' => [
                'setCampaign' => $paramsCampaign,
                'setProductFamilyId' => $familyId,
                'setSku' => $sku,
                'setTitle' => $title,
                'setIsbn' => $isbn,
                'setPrice' => $price,
            ],
        ];

        $expectedBook = [
            'id' => null,
            'product-family-id' => $familyId,
            'sku' => $sku,
            'title' => $title,
            'author' => null,
            'class' => null,
            'isbn' => $isbn,
            'price' => $price,
            'quantity' => 1,
            'status' => CampaignBook::STATUS_AVAILABLE,
            'status-name' => 'STATUS_AVAILABLE',
            'available' => true,
            'state' => CampaignBook::STATE_UNKNOWN,
            'state-name' => 'STATE_UNKNOWN',
            'created-at' => null,
            'updated-at' => null,
        ];

        $expectedCampaign = [
            'id' => null,
            'user' => null,
            'start-date' => $startDate->format(DateHelper::PHP_STANDARD_FORMAT),
            'end-date' => $endDate->format(DateHelper::PHP_STANDARD_FORMAT),
            'shipping-option' => null,
            'estimated-shipping-price' => '$0',
            'estimated-shipping' => null,
            'estimated-tax-price' => '$0',
            'estimated-tax' => null,
            'estimated-cost-price' => '$1',
            'estimated-cost' => $estimatedCost,
            'funded-total' => 0,
            'status' => 0,
            'inactive' => true,
            'active' => false,
            'finished' => false,
            'paused' => false,
            'percent-of-funded' => 0.5,
            'major' => null,
            'created-at' => null,
            'updated-at' => null,
            'contacts' => [],
            'books' => [],
            'campaign-goal' => 100,
            'paused-at' => null,
            'orders' => [],
            'orders-total' => 0
        ];


        // 1. Book with 1 level
        $result[] = [
            'params' => $paramsBook,
            'maxLevel' => 1,
            'expected' => $expectedBook,
        ];


        // 2. Book with 2 levels
        $newExpectedBook = $expectedBook;
        $newExpectedBook['campaign'] = $expectedCampaign;

        $result[] = [
            'params' => $paramsBook,
            'maxLevel' => 2,
            'expected' => $newExpectedBook,
        ];


        // 3. Campaign with collection
        $newParamsCampaign = $paramsCampaign;
        $newParamsCampaign['methods']['setBooks'] = [
            'isCollection' => true,
            'class' => $paramsBook['class'],
            'elements' => [
                $paramsBook['methods'],
            ],
        ];

        $newExpectedCampaign = $expectedCampaign;
        $newExpectedCampaign['books'] = [$expectedBook];

        $result[] = [
            'params' => $newParamsCampaign,
            'maxLevel' => 2,
            'expected' => $newExpectedCampaign,
        ];

        return $result;
    }
}
