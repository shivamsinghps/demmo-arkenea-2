<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\RtpDetails;

/**
 * Class RtpDetailsMapper
 */
class RtpDetailsMapper extends AbstractMapper
{
    /**
     * @param RtpDetails $source
     *
     * @return array
     */
    public function mapToArray(RtpDetails $source): array
    {
        return [
            'destination' => [
                'remittanceData' => $source->getDestinationRemittanceData(),
            ],
        ];
    }

    /**
     * @param array $source
     *
     * @return RtpDetails
     */
    public function mapFromArray(array $source): RtpDetails
    {
        $result = new RtpDetails();

        if (isset($source['destination']['remittanceData']) && !is_null($source['destination']['remittanceData'])) {
            $result->setDestinationRemittanceData($source['destination']['remittanceData']);
        }

        return $result;
    }
}
