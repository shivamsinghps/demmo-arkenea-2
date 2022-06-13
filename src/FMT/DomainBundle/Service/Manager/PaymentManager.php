<?php
/**
 * Author: Anton Orlov
 * Date: 23.04.2018
 * Time: 11:57
 */

namespace FMT\DomainBundle\Service\Manager;

use FMT\DataBundle\Entity\CampaignBook;
use FMT\DataBundle\Entity\CampaignContact;
use FMT\DataBundle\Entity\OrderItem;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Entity\UserTransaction;
use FMT\DomainBundle\Event\TransactionEvent;
use FMT\DomainBundle\Exception\InvalidDonationException;
use FMT\DomainBundle\Exception\CartActionException;
use FMT\DomainBundle\Exception\InvalidReturnOrderItemException;
use FMT\DomainBundle\Exception\PaymentException;
use FMT\DomainBundle\Repository\UserTransactionRepositoryInterface;
use FMT\DomainBundle\Service\PaymentManagerInterface;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\DomainBundle\Type\Payment\Donation;
use FMT\DomainBundle\Type\Payment\Settings as PaymentSettings;
use FMT\InfrastructureBundle\Helper\LogHelper;

/**
 * Class PaymentManager
 * @package FMT\DomainBundle\Service\Manager
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PaymentManager extends EventBasedManager implements PaymentManagerInterface
{
    /** @var PaymentSettings */
    private $settings;

    /** @var UserTransactionRepositoryInterface */
    private $repository;

    /** @var UserManagerInterface */
    private $userManager;

    public function __construct(PaymentSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param UserTransactionRepositoryInterface $repository
     * @required
     */
    public function setRepository(UserTransactionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UserManagerInterface $manager
     * @required
     */
    public function setUserManager(UserManagerInterface $manager)
    {
        $this->userManager = $manager;
    }

    /**
     * @param int $id
     * @return UserTransaction
     */
    public function getTransaction($id)
    {
        /** @var UserTransaction $result */
        $result = $this->repository->findById($id);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function sendRefundFromOrderItemReturn(OrderItem $orderItem): void
    {
        $this->validateReturnOrderItem($orderItem);

        $transaction = new UserTransaction();

        try {
            $this->repository->beginTransaction();
            $campaign = $orderItem->getOrder()->getCampaign();
            $lastCampaign = $campaign->getUser()->getCampaigns()->first();

            $transaction
                ->setType(UserTransaction::TXN_BOOK_REFUND)
                ->setAnonymous(true)
                ->setOrder($orderItem->getOrder())
                ->setCampaign($lastCampaign)
                ->setRecipient($lastCampaign->getUser())
                ->setFee(0)
                ->setNet($orderItem->getPrice())
            ;

            $orderItem->setStatus(OrderItem::STATUS_RETURNED);
            $orderItem->getBook()->setStatus(CampaignBook::STATUS_RETURNED);
            $orderItem->setUnprocessedAmount($orderItem->getPrice());

            $this->repository->save($transaction);
            $this->repository->save();
            $this->repository->commit();
        } catch (\Exception $exception) {
            LogHelper::critical((array) $exception);
            $this->repository->rollback();

            throw $exception;
        }
    }

    /**
     * @inheritdoc
     */
    public function createTransaction(Order $order): void
    {
        $transaction = new UserTransaction();

        try {
            $this->repository->beginTransaction();
            $campaign = $order->getCampaign();
            $transaction
                ->setType(UserTransaction::TXN_DIRECT_PURCHASE)
                ->setAnonymous(true)
                ->setOrder($order)
                ->setCampaign($campaign)
                ->setRecipient($campaign->getUser())
                ->setFee(0)
                ->setNet(-$order->getTotal())
            ;

            $this->repository->save($transaction);
            $this->repository->commit();
        } catch (\Exception $exception) {
            LogHelper::critical((array) $exception);
            $this->repository->rollback();

            throw $exception;
        }
    }

    /**
     * @param Donation $donation
     * @return UserTransaction
     * @throws PaymentException
     * @throws InvalidDonationException
     */
    public function sendDonation(Donation $donation)
    {
        $this->validateDonation($donation);

        $donor = $donation->getDonor();
        $recipient = $donation->getStudent();

        $transaction = new UserTransaction();

        try {
            $this->repository->beginTransaction();

            $transaction->setType(UserTransaction::TXN_DONATION);
            $transaction->setSender($donor);
            $transaction->setRecipient($recipient);
            $transaction->setCampaign($recipient->getUnfinishedCampaign());
            $transaction->setNet($donation->getPaymentAmountCents());
            $transaction->setFee($this->settings->application->fee($donation->getPaymentAmountCents()));
            $transaction->setPaymentSystemFee($this->settings->paymentService->fee($transaction->getAmount()));
            $transaction->setAnonymous($donation->isAnonymous());

            LogHelper::debug("Creating donor transaction: %d + %d", $transaction->getNet(), $transaction->getFee());

            $this->repository->save($transaction);

            $this->dispatch(
                TransactionEvent::TRANSACTION_STARTED,
                new TransactionEvent($transaction)
            );

            if (!$donation->isAnonymous() && $donor instanceof User && $donor !== $recipient) {
                $contact = $this->userManager->addContact($recipient, $donor, true);
                $contact->getCampaignContact()->setStatus(CampaignContact::STATUS_CONFIRMED);
                $this->repository->save($contact->getCampaignContact());
            }

            $this->repository->save(
                $donation->getPaymentProcessor()->charge($transaction)
            );

            $this->repository->commit();
        } catch (PaymentException|\Exception $exception) {
            LogHelper::debug($donation);
            LogHelper::critical($exception);

            $this->repository->rollback();

            $this->dispatch(
                TransactionEvent::TRANSACTION_FAILED,
                new TransactionEvent($transaction)
            );

            throw $exception;
        }

        $this->dispatch(
            TransactionEvent::TRANSACTION_COMPLETED,
            new TransactionEvent($transaction)
        );

        return $transaction;
    }

    /**
     * @param int $amount in cents
     * @return array
     */
    public function getDonationFees(int $amount): array
    {
        $fmtFee = $this->settings->application->fee($amount);
        return [
            'fmtFee' => $fmtFee,
            'paymentSystemFee' => $this->settings->paymentService->fee($amount + $fmtFee),
        ];
    }

    /**
     * @param Donation $donation
     * @throws InvalidDonationException
     */
    protected function validateDonation(Donation $donation)
    {
        $recipient = $donation->getStudent();

        $unfinishedCampaign = $recipient->getUnfinishedCampaign();

        if (!$recipient->isActiveStudent() || !$unfinishedCampaign || $unfinishedCampaign->isPaused()) {
            throw new InvalidDonationException("Unsupported student");
        }

        if ($donation->getPaymentAmountCents() <= 0) {
            throw new InvalidDonationException("Donation amount could not be fewer or equal zero");
        }
    }

    protected function validateReturnOrderItem(OrderItem $orderItem): void
    {
        if ($orderItem->getStatus() !== OrderItem::STATUS_PRE_RETURNED) {
            throw new InvalidReturnOrderItemException();
        }
    }

    /**
     * @param Donation $donation
     * @throws InvalidDonationException
     * @throws CartActionException
     */
    protected function validatePaymentForOrder(Donation $donation, Order $order)
    {
        $recipient = $donation->getStudent();

        $unfinishedCampaign = $recipient->getUnfinishedCampaign();

        if (!$recipient->isActiveStudent() || !$unfinishedCampaign || $unfinishedCampaign->isPaused()) {
            throw new InvalidDonationException("Unsupported student");
        }

        if ($order->getTotal() <= 0) {
            throw new CartActionException("Cart total could not be fewer or equal zero");
        }
    }

    /**
     * @param Donation $donation
     * @param Order $order
     * @return UserTransaction
     * @throws PaymentException
     * @throws InvalidDonationException
     */
    public function sendPaymentForOrder(Donation $donation, Order $order)
    {
        $this->validatePaymentForOrder($donation, $order);

        $donor = $donation->getDonor();
        $recipient = $donation->getStudent();

        $transaction = new UserTransaction();

        try {
            $this->repository->beginTransaction();
            $recipient->getStatistic()->setAmountFounded(
                $recipient->getStatistic()->getAmountFounded() + $order->getPrice());

            $transaction->setType(UserTransaction::TXN_BOOK_PURCHASE);
            $transaction->setOrder($order);
            $transaction->setSender($donor);
            $transaction->setRecipient($recipient);
            $transaction->setCampaign($recipient->getUnfinishedCampaign());
            $transaction->setNet($order->getTotal());
            $transaction->setFee($this->settings->application->fee($order->getTotal()));
            $transaction->setPaymentSystemFee($this->settings->paymentService->fee($transaction->getAmount()));
            $transaction->setAnonymous($donation->isAnonymous());

            LogHelper::debug("Creating checkout transaction: %d + %d", $transaction->getNet(), $transaction->getFee());

            $this->repository->save($transaction);

            $this->dispatch(
                TransactionEvent::TRANSACTION_STARTED,
                new TransactionEvent($transaction)
            );

            if (!$donation->isAnonymous() && $donor instanceof User && $donor !== $recipient) {
                $contact = $this->userManager->addContact($recipient, $donor, true);
                $contact->getCampaignContact()->setStatus(CampaignContact::STATUS_CONFIRMED);
                $this->repository->save($contact->getCampaignContact());
            }

            $this->repository->save(
                $donation->getPaymentProcessor()->charge($transaction)
            );

            $this->repository->commit();
        } catch (PaymentException|\Exception $exception) {
            LogHelper::debug($donation);
            LogHelper::critical($exception);

            $this->repository->rollback();

            $this->dispatch(
                TransactionEvent::TRANSACTION_FAILED,
                new TransactionEvent($transaction)
            );

            throw $exception;
        }

        $this->dispatch(
            TransactionEvent::TRANSACTION_COMPLETED,
            new TransactionEvent($transaction)
        );

        return $transaction;
    }
}
