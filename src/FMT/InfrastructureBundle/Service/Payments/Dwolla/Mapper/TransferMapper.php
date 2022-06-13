<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use DateTime;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\AchDetails;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Amount;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Clearing;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Fee;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\RtpDetails;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Transfer;

/**
 * Class TransferMapper
 */
class TransferMapper extends AbstractMapper
{
    /**
     * @param Transfer $source
     *
     * @return array
     */
    public function mapToArray(Transfer $source): array
    {
        $result = [
            '_links' => [
                'source' => [
                    'href' => $source->getSource(),
                ],
                'destination' => [
                    'href' => $source->getDestination(),
                ],
            ],
            'amount' => $this->mapper->map($source->getAmount(), 'array'),
        ];

        if (!is_null($source->getMetadata())) {
            $result['metadata'] = $source->getMetadata();
        }

        if (!is_null($source->getFees())) {
            $result['fees'] = array_map(function (Fee $fee) {
                return $this->mapper->map($fee, 'array');
            }, $source->getFees());
        }

        if (!is_null($source->getClearing())) {
            $result['clearing'] = $this->mapper->map($source->getClearing(), 'array');
        }

        if (!is_null($source->getAchDetails())) {
            $result['achDetails'] = $this->mapper->map($source->getAchDetails(), 'array');
        }

        if (!is_null($source->getRtpDetails())) {
            $result['rtpDetails'] = $this->mapper->map($source->getRtpDetails(), 'array');
        }

        if (!is_null($source->getCorrelationId())) {
            $result['correlationId'] = $source->getCorrelationId();
        }

        if (!is_null($source->getProcessingChannel())) {
            $result['processingChannel'] = $source->getProcessingChannel();
        }

        return $result;
    }

    /**
     * @param array $source
     *
     * @return Transfer
     */
    public function mapFromArray(array $source): Transfer
    {
        $result = new Transfer();
        $result
            ->setId($source['id'])
            ->setIri($source['_links']['self']['href'])
            ->setSource($source['_links']['source-funding-source']['href'])
            ->setDestination($source['_links']['destination-funding-source']['href'])
            ->setStatus($source['status'])
            ->setAmount($this->mapper->map($source['amount'], Amount::class))
            ->setCreated(new DateTime($source['created']['date']))
            ->setMetadata($source['metadata'] ?? null)
            ->setCorrelationId($source['correlation_id'] ?? null)
            ->setIndividualAchId($source['individual_ach_id'] ?? null)
        ;

        if (isset($source['clearing']) && !is_null($source['clearing'])) {
            $result->setClearing($this->mapper->map($source['clearing'], Clearing::class));
        }

        if (isset($source['ach_details']) && !is_null($source['ach_details'])) {
            $result->setAchDetails($this->mapper->map($source['ach_details'], AchDetails::class));
        }

        if (isset($source['rtp_details']) && !is_null($source['rtp_details'])) {
            $result->setRtpDetails($this->mapper->map($source['rtp_details'], RtpDetails::class));
        }

        return $result;
    }
}
