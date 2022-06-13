<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Fee;

/**
 * Class FeeMapper
 */
class FeeMapper extends AbstractMapper
{
    /**
     * @param Fee $source
     *
     * @return array
     */
    public function map(Fee $source): array
    {
        return [
            '_links' => [
                'charge-to' => [
                    'href' => $source->getChargeTo(),
                ],
            ],
            'amount' => $this->mapper->map($source->getAmount(), 'array'),
        ];
    }
}
