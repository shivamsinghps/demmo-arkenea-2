<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\BookstorePayment;

/**
 * Class SuccessTransfer
 */
class SuccessTransfer
{
    /**
     * @var int
     */
    protected $amount;

    /**
     * @var int
     */
    protected $purchaseAmount;

    /**
     * @var int
     */
    protected $refundAmount;

    /**
     * @var int
     */
    protected $rejectedTransfersAmount;

    /**
     * @param int $amount
     * @param int $purchaseAmount
     * @param int $refundAmount
     * @param int $rejectedTransfersAmount
     */
    public function __construct(int $amount, int $purchaseAmount, int $refundAmount, int $rejectedTransfersAmount)
    {
        $this->amount = $amount;
        $this->purchaseAmount = $purchaseAmount;
        $this->refundAmount = $refundAmount;
        $this->rejectedTransfersAmount = $rejectedTransfersAmount;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getPurchaseAmount(): int
    {
        return $this->purchaseAmount;
    }

    /**
     * @return int
     */
    public function getRefundAmount(): int
    {
        return $this->refundAmount;
    }

    /**
     * @return int
     */
    public function getRejectedTransfersAmount(): int
    {
        return $this->rejectedTransfersAmount;
    }
}
