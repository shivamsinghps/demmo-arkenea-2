<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\CardDetails;

/**
 * Class CardDetailsMapper
 */
class CardDetailsMapper extends AbstractMapper
{
    /**
     * @param array $source
     *
     * @return CardDetails
     */
    public function map(array $source): CardDetails
    {
        $result = new CardDetails();
        $result
            ->setBrand($source['brand'])
            ->setLastFour($source['lastFour'])
            ->setExpirationMonth((int) $source['expirationMonth'])
            ->setExpirationYear((int) $source['expirationYear'])
            ->setNameOnCard($source['nameOnCard'])
        ;
        
        return $result;
    }
}
