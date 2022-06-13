<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Exception\Exception;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\AchDetails;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Addenda;

/**
 * Class AchDetailsMapper
 */
class AchDetailsMapper extends AbstractMapper
{
    /**
     * @param AchDetails $source
     *
     * @return array
     * @throws Exception
     */
    public function mapToArray(AchDetails $source): array
    {
        $result = [];

        if (!is_null($source->getSource())) {
            $result['source']['addenda'] = $this->mapper->map($source->getSource(), 'array');
        }

        if (!is_null($source->getDestination())) {
            $result['destination']['addenda'] = $this->mapper->map($source->getDestination(), 'array');
        }

        if (empty($result)) {
            throw new Exception('AchDetails must contain one of the following fields: "source", "destination".');
        }

        return $result;
    }

    /**
     * @param array $source
     *
     * @return AchDetails
     */
    public function mapFromArray(array $source): AchDetails
    {
        $result = new AchDetails();

        if (isset($source['source']) && !is_null($source['source'])) {
            $result->setSource($this->mapper->map($source['source'], Addenda::class));
        }

        if (isset($source['destination']) && !is_null($source['destination'])) {
            $result->setSource($this->mapper->map($source['destination'], Addenda::class));
        }

        return $result;
    }
}
