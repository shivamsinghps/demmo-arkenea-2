<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Exception\Exception;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Clearing;

/**
 * Class ClearingMapper
 */
class ClearingMapper extends AbstractMapper
{
    /**
     * @param Clearing $source
     *
     * @return array
     * @throws Exception
     */
    public function mapToArray(Clearing $source): array
    {
        $result = [];

        if (!is_null($source->getSource())) {
            $result['source'] = $source->getSource();
        }

        if (!is_null($source->getDestination())) {
            $result['destination'] = $source->getDestination();
        }

        if (empty($result)) {
            throw new Exception('Clearing must contain one of the following fields: "source", "destination".');
        }

        return $result;
    }

    /**
     * @param array $source
     *
     * @return Clearing
     */
    public function mapFromArray(array $source): Clearing
    {
        return new Clearing($source['source'], $source['destination']);
    }
}
