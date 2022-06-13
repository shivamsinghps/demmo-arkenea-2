<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\BookstorePayment;

use Exception;
use FMT\DataBundle\Entity\BookstoreTransfer;
use FMT\DomainBundle\Repository\BookstoreTransferRepositoryInterface;
use FMT\InfrastructureBundle\Helper\LogHelper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Client;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Transfer;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Class TransferManager
 */
class TransferManager
{
    /**
     * @var BookstoreTransferRepositoryInterface
     */
    protected $bookstoreTransferRepository;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param BookstoreTransferRepositoryInterface $bookstoreTransferRepository
     * @param Client                               $client
     */
    public function __construct(BookstoreTransferRepositoryInterface $bookstoreTransferRepository, Client $client)
    {
        $this->bookstoreTransferRepository = $bookstoreTransferRepository;
        $this->client = $client;
    }

    /**
     * @param string $transferId
     */
    public function transferCompleted(string $transferId): void
    {
        $bookstoreTransfer = $this->getBookstoreTransfer($transferId);

        if (!$bookstoreTransfer instanceof BookstoreTransfer) {
            return;
        }

        $bookstoreTransfer->setStatus(BookstoreTransfer::STATUS_PROCESSED);
        $this->bookstoreTransferRepository->save($bookstoreTransfer);
    }

    /**
     * @param string $transferId
     */
    public function transferRejected(string $transferId): void
    {
        $bookstoreTransfer = $this->getBookstoreTransfer($transferId);

        if (!$bookstoreTransfer instanceof BookstoreTransfer) {
            return;
        }

        $bookstoreTransfer->setStatus(BookstoreTransfer::STATUS_REJECTED);
        $this->bookstoreTransferRepository->save($bookstoreTransfer);
    }

    /**
     * @param string $transferId
     *
     * @return BookstoreTransfer|null
     */
    protected function getBookstoreTransfer(string $transferId): ?BookstoreTransfer
    {
        try {
            $transfer = $this->client->getTransferById($transferId);

            if (!$transfer instanceof Transfer) {
                throw new NotFoundResourceException();
            }

            $id = (int) $transfer->getMetadata()['bookstore_transfer_id'];
            $bookstoreTransfer = $this->bookstoreTransferRepository->findById($id);

            if (!$bookstoreTransfer instanceof BookstoreTransfer) {
                throw new NotFoundResourceException();
            }
        } catch (Exception $e) {
            LogHelper::critical([sprintf('Problem with change transfer#%s status', $transferId)]);

            return null;
        }

        return $bookstoreTransfer;
    }
}
