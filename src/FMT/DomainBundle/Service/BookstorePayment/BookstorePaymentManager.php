<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\BookstorePayment;

use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use FMT\DataBundle\Entity\BookstoreTransfer;
use FMT\DomainBundle\Repository\BookstoreTransferRepositoryInterface;
use FMT\DomainBundle\Service\BookstorePaymentManagerInterface;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Client;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Amount;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Transfer;

/**
 * Class BookstorePaymentManager
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class BookstorePaymentManager implements BookstorePaymentManagerInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var CustomerManager
     */
    protected $customerManager;

    /**
     * @var BookstoreTransferRepositoryInterface
     */
    protected $bookstoreTransferRepository;

    /**
     * @var TransactionCollector
     */
    protected $transactionCollector;

    /**
     * @var WebhooksSubscriber
     */
    protected $webhooksSubscriber;

    /**
     * @var NotificatorInterface
     */
    protected $notificator;

    /**
     * @var string
     */
    protected $selfFundingSourceIri;

    /**
     * @param Client                               $client
     * @param CustomerManager                      $customerManager
     * @param BookstoreTransferRepositoryInterface $bookstoreTransferRepository
     * @param TransactionCollector                 $transactionCollector
     * @param WebhooksSubscriber                   $webhooksSubscriber
     * @param NotificatorInterface                 $notificator
     * @param string                               $selfFundingSourceIri
     */
    public function __construct(
        Client $client,
        CustomerManager $customerManager,
        BookstoreTransferRepositoryInterface $bookstoreTransferRepository,
        TransactionCollector $transactionCollector,
        WebhooksSubscriber $webhooksSubscriber,
        NotificatorInterface $notificator,
        string $selfFundingSourceIri
    ) {
        $this->client = $client;
        $this->customerManager = $customerManager;
        $this->bookstoreTransferRepository = $bookstoreTransferRepository;
        $this->transactionCollector = $transactionCollector;
        $this->webhooksSubscriber = $webhooksSubscriber;
        $this->notificator = $notificator;
        $this->selfFundingSourceIri = $selfFundingSourceIri;
    }

    /**
     * @inheritDoc
     */
    public function sendTransfer(SendTime $sendTime, ?bool $validTime = true): bool
    {
        if ($validTime && !$this->canSendTransfer($sendTime)) {
            return false;
        }
        
        $this->webhooksSubscriber->subscribe();
        $this->notificator->transferSend($this->createTransfer());

        return true;
    }

    /**
     * @throws OptimisticLockException
     */
    protected function createTransfer(): SuccessTransfer
    {
        $entityManager = $this->bookstoreTransferRepository->getEm();
        $entityManager->beginTransaction();

        try {
            $bookstoreTransfer = new BookstoreTransfer();
            $successTransfer = $this->transactionCollector->processBookstoreTransactions($bookstoreTransfer);
            $entityManager->persist($bookstoreTransfer);
            $entityManager->flush();

            if ($bookstoreTransfer->getNet() > 0) {
                $transfer = new Transfer();
                $transfer
                    ->setSource($this->selfFundingSourceIri)
                    ->setDestination($this->customerManager->getFundingSourceIri())
                    ->setAmount(new Amount($bookstoreTransfer->getNet(), Amount::CURRENCY_USD))
                    ->setMetadata(['bookstore_transfer_id' => $bookstoreTransfer->getId()])
                ;
                $this->client->createTransfer($transfer);
            }

            $entityManager->commit();

            return $successTransfer;
        } catch (Exception $e) {
            $entityManager->rollback();

            throw $e;
        }
    }

    /**
     * @param SendTime $sendTime
     *
     * @return bool
     */
    protected function canSendTransfer(SendTime $sendTime): bool
    {
        $lastBookstoreTransfer = $this->bookstoreTransferRepository->findLastCreated();
        $pauseLeft = new DateTime();
        $pauseIntervalLeft = true;

        if ($lastBookstoreTransfer instanceof BookstoreTransfer) {
            $createdAt = $lastBookstoreTransfer->getCreatedAt();
            $pauseLeft = $createdAt->add($sendTime->getPauseInterval());
            $pauseIntervalLeft = $pauseLeft <= new DateTime();
        }

        if (!$pauseIntervalLeft) {
            return false;
        }

        return $sendTime->getTime($pauseLeft) <= new DateTime();
    }
}
