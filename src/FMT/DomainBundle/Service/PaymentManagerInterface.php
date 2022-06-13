<?php
/**
 * Author: Anton Orlov
 * Date: 23.04.2018
 * Time: 11:57
 */

namespace FMT\DomainBundle\Service;

use FMT\DataBundle\Entity\OrderItem;
use FMT\DataBundle\Entity\UserTransaction;
use FMT\DataBundle\Entity\Order;
use FMT\DomainBundle\Exception\InvalidDonationException;
use FMT\DomainBundle\Exception\InvalidReturnOrderItemException;
use FMT\DomainBundle\Exception\PaymentException;
use FMT\DomainBundle\Type\Payment\Donation;

interface PaymentManagerInterface
{
    /**
     * @param int $id
     * @return UserTransaction
     */
    public function getTransaction($id);

    /**
     * @param Donation $donation
     * @return UserTransaction
     * @throws PaymentException
     * @throws InvalidDonationException
     */
    public function sendDonation(Donation $donation);

    /**
     * @param OrderItem $orderItem
     * @throws InvalidReturnOrderItemException
     */
    public function sendRefundFromOrderItemReturn(OrderItem $orderItem): void;

    /**
     * Calculate fees for any donation amount
     * @param int $amountCents
     * @return array
     */
    public function getDonationFees(int $amountCents): array;

    /**
     * @param Donation $donation
     * @param Order $order
     * @return UserTransaction
     * @throws PaymentException
     * @throws InvalidDonationException
     */
    public function sendPaymentForOrder(Donation $donation, Order $order);
}
