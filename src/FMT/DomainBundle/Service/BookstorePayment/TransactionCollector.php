<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\BookstorePayment;

use FMT\DataBundle\Entity\BookstoreTransfer;
use FMT\DataBundle\Entity\UserTransaction;
use FMT\DomainBundle\Repository\BookstoreTransferRepositoryInterface;
use FMT\DomainBundle\Repository\OrderItemRepositoryInterface;
use FMT\DomainBundle\Repository\OrderRepositoryInterface;

/**
 * Class TransactionCollector
 */
class TransactionCollector
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderItemRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var BookstoreTransferRepositoryInterface
     */
    protected $transferRepository;

    /**
     * TransactionCollector constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param BookstoreTransferRepositoryInterface $bookstoreTransferRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderItemRepositoryInterface $orderItemRepository,
        BookstoreTransferRepositoryInterface $bookstoreTransferRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->transferRepository = $bookstoreTransferRepository;
    }

    /**
     * @param BookstoreTransfer $transfer
     *
     * @return SuccessTransfer
     */
    public function processBookstoreTransactions(BookstoreTransfer $transfer): SuccessTransfer
    {
        $transferNet = 0;
        $rejectedTransfersAmount = 0;
        $purchaseAmount = 0;
        $refundAmount = 0;

        foreach ($this->collectRejectedTransfers() as $rejectedTransfer) {
            $transferNet += $rejectedTransfer->getNet();
            $rejectedTransfersAmount += $rejectedTransfer->getNet();
            $transfer->addChild($rejectedTransfer);
        }

        foreach ($this->collectCheckoutTransactions() as $checkoutTransaction) {
            $transferNet += $checkoutTransaction->getUnprocessedAmount();
            $purchaseAmount += $checkoutTransaction->getUnprocessedAmount();
            $checkoutTransaction->setUnprocessedAmount(0);
        }

        foreach ($this->collectRefundedTransactions() as $refundedTransaction) {
            if ($transferNet === 0) {
                break;
            }

            $unprocessedNet = $refundedTransaction->getUnprocessedAmount();
            $processNet = $unprocessedNet > $transferNet ? $transferNet : $unprocessedNet;
            $transferNet -= $processNet;
            $refundAmount += $processNet;
            $refundedTransaction->setUnprocessedAmount($unprocessedNet - $processNet);
        }

        if ($transferNet === 0) {
            $transfer->setStatus(BookstoreTransfer::STATUS_PROCESSED);
        }

        $transfer->setNet($transferNet);

        return new SuccessTransfer($transferNet, $purchaseAmount, $refundAmount, $rejectedTransfersAmount);
    }

    protected function collectCheckoutTransactions()
    {
        return $this->orderRepository->findCheckout();
    }

    protected function collectRefundedTransactions()
    {
        return $this->orderItemRepository->findReturn();
    }

    /**
     * @return BookstoreTransfer[]
     */
    protected function collectRejectedTransfers(): array
    {
        return $this->transferRepository->findWithoutParentByStatus(BookstoreTransfer::STATUS_REJECTED);
    }
}
