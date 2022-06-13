<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Api;

use DwollaSwagger\ApiException;
use DwollaSwagger\TransfersApi;
use FMT\InfrastructureBundle\Service\Mapper\Mapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Transfer;

/**
 * Trait TransfersTrait
 */
trait TransfersTrait
{
    /**
     * @return Mapper
     */
    protected abstract function getMapper(): Mapper;

    /**
     * @var TransfersApi|null
     */
    private $transferApi;

    /**
     * @param Transfer $transfer
     *
     * @return string
     */
    public function createTransfer(Transfer $transfer): string
    {
        $body = $this->getMapper()->map($transfer, 'array');

        return (string) $this->getTransferApi()->create($body);
    }

    /**
     * @param string $transferId
     *
     * @return Transfer
     */
    public function getTransferById(string $transferId): ?Transfer
    {
        try {
            $result = $this->getTransferApi()->byId($transferId);

            return $this->getMapper()->map(json_decode(json_encode($result), true), Transfer::class);
        } catch (ApiException $e) {
            if ($e->getCode() === 404) {
                return null;
            }

            throw $e;
        }
    }

    /**
     * @return TransfersApi
     */
    private function getTransferApi(): TransfersApi
    {
        if (is_null($this->transferApi)) {
            $this->transferApi = new TransfersApi();
        }

        return $this->transferApi;
    }
}
