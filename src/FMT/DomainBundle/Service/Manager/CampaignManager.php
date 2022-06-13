<?php

namespace FMT\DomainBundle\Service\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\CampaignBook;
use FMT\DataBundle\Entity\CampaignContact;
use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Entity\OrderItem;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserContact;
use FMT\DataBundle\Entity\UserProfile;
use FMT\DataBundle\Model\BaseFilterOptions;
use FMT\DomainBundle\Event\CampaignEvent;
use FMT\DomainBundle\Exception\CartConfigurationException;
use FMT\DomainBundle\Repository\CampaignRepositoryInterface;
use FMT\DomainBundle\Service\BookManagerInterface;
use FMT\DomainBundle\Service\CampaignManagerInterface;
use FMT\DomainBundle\Service\CartManagerInterface;
use FMT\DomainBundle\Service\PaymentManagerInterface;
use FMT\DomainBundle\Service\ShippingManagerInterface;
use FMT\InfrastructureBundle\Helper\DateHelper;
use FMT\InfrastructureBundle\Helper\LogHelper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CampaignManager
 * @package FMT\DomainBundle\Service\Manager
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CampaignManager extends EventBasedManager implements CampaignManagerInterface
{
    const DEFAULT_SHIPPING_ID = 208;

    /** @var CartManagerInterface */
    protected $cartManager;

    /** @var CampaignRepositoryInterface */
    private $repository;

    /** @var BookManagerInterface */
    private $bookManager;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PaymentManagerInterface
     */
    private $paymentManager;

    /**
     * @var ShippingManagerInterface
     */
    private $shippingManager;

    /**
     * CampaignManager constructor.
     * @param CampaignRepositoryInterface $repository
     * @param BookManagerInterface $bookManager
     * @param CartManagerInterface $manager
     * @param TokenStorageInterface $tokenStorage
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CampaignRepositoryInterface $repository,
        BookManagerInterface $bookManager,
        CartManagerInterface $manager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->repository = $repository;
        $this->bookManager = $bookManager;
        $this->cartManager = $manager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param TranslatorInterface $translator
     * @required
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param PaymentManagerInterface $paymentManager
     * @required
     */
    public function setPaymentManager(PaymentManagerInterface $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    /**
     * @param ShippingManagerInterface $shippingManager
     * @required
     */
    public function setShippingManager(ShippingManagerInterface $shippingManager)
    {
        $this->shippingManager = $shippingManager;
    }

    /**
     * @param User $user
     * @return Campaign
     */
    public function prepareNew(User $user)
    {
        $campaign = new Campaign();
        $campaign->setUser($user);
        $campaign->setShippingOption($this->setDefaultShipping());

        return $campaign;
    }

    /**
     * @param Campaign $campaign
     * @return bool
     * @throws \Exception
     */
    public function create(Campaign $campaign)
    {
        $event = new CampaignEvent($campaign);

        try {
            $this->updateBooksInfo($campaign);
            $this->updateTotals($campaign);

            $this->repository->save($campaign);
        } catch (\Exception $exception) {
            $this->dispatch(CampaignEvent::CAMPAIGN_FAILED, $event);
            throw $exception;
        }

        $this->dispatch(CampaignEvent::CAMPAIGN_CREATED, $event);

        return true;
    }

    /**
     * @param Campaign $campaign
     * @throws CartConfigurationException
     */
    public function updateTotals(Campaign $campaign)
    {
        if (!$campaign->getId()) {
            $spend = 0;
            $user = $campaign->getUser();
            if ($lastFinishedCampaign = $this->repository->getLastFinishedCampaign($user)) {
                $spend = $lastFinishedCampaign->getFundedTotal();
            }

            $campaign->setDonationsFromPrevious($spend);
        }

        $summary = $this->cartManager->estimate($campaign->getBooks()->toArray());

        $campaign->setEstimatedShipping($summary->getShipping());
        $campaign->setEstimatedCost($summary->getSubtotal());
        $campaign->recalcTotalsByTransactions();
    }

    /**
     * @param Campaign $campaign
     * @return bool
     * @throws \Exception
     */
    public function update(Campaign $campaign)
    {
        $event = new CampaignEvent($campaign);

        try {
            $this->updateBooksInfo($campaign);
            $this->updateTotals($campaign);

            $this->repository->save();
        } catch (\Exception $exception) {
            $this->dispatch(CampaignEvent::CAMPAIGN_FAILED, $event);
            throw $exception;
        }

        $this->dispatch(CampaignEvent::CAMPAIGN_UPDATED, $event);

        return true;
    }

    /**
     * @return CampaignRepositoryInterface
     * @deprecated DIRECT USAGE OF REPOSITORY IS NOT ALLOWED
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param Campaign $campaign
     * @param UserContact $contact
     * @return CampaignContact
     */
    public function assignContact(Campaign $campaign, UserContact $contact)
    {
        if (!$campaign->isActive() && $campaign->isFinished()) {
            throw new \RuntimeException("Unable to add contact into inactive campaign");
        }

        if (!($result = $campaign->findContact($contact))) {
            $result = $campaign->addContact($contact);
            $this->repository->save($campaign);
            $this->dispatch(CampaignEvent::CAMPAIGN_CONTACT_ADDED, new CampaignEvent($campaign));
        }

        return $result;
    }

    /**
     * @param Campaign $campaign
     * @return void
     */
    public function updateMassMailedCalled(Campaign $campaign) {
        $campaign->setMassMailedCalled(true);
        $this->repository->save();
    }

    /**
     * @param Campaign $campaign
     */
    public function togglePauseStatus(Campaign $campaign)
    {
        $campaign->togglePaused();
        if ($campaign->isPaused()) {
            $campaign->setPausedAt(new \DateTime());
        }

        $this->repository->save();

        if ($campaign->isPaused()) {
            $this->dispatch(CampaignEvent::CAMPAIGN_PAUSED, new CampaignEvent($campaign));
        } else {
            $this->dispatch(CampaignEvent::CAMPAIGN_RESTARTED, new CampaignEvent($campaign));
        }
    }

    /**
     * @param BaseFilterOptions $formFilterParams
     * @return QueryBuilder
     */
    public function getByFilter(BaseFilterOptions $formFilterParams)
    {
        return $this->repository->getCampaignByFilter($formFilterParams, $this->getVisibilityData());
    }

    /**
     * @inheritDoc
     */
    public function getTotalCountByFilter(BaseFilterOptions $formFilterParams)
    {
        return $this->repository->getTotalByFilter($formFilterParams, $this->getVisibilityData());
    }

    /**
     * @inheritDoc
     */
    public function validateDonateAmount(Campaign $campaign, string $stringAmount): array
    {
        $stringAmountParts = explode('.', $stringAmount);
        $stringAmountCents = $stringAmountParts[1] ?? '';
        $stringAmountInCents = $stringAmountParts[0] . str_pad($stringAmountCents, 2, '0');

        $allowedToDonate = $campaign->getAllowedDonateAmount();
        $success = strlen($stringAmountInCents) <= strlen((string) $allowedToDonate)
            && (int) $stringAmountInCents <= $allowedToDonate;
        $validateResult = ['success' => $success];

        if (!$success) {
            $validateResult['reason'] = $this->translator->trans('fmt.campaign.too_much_amount', [
                '{amount}' => $allowedToDonate / 100
            ]);
        } else {
            $validateResult = array_merge(
                $validateResult,
                $this->paymentManager->getDonationFees($stringAmountInCents)
            );
        }

        return $validateResult;
    }

    /**
     * @param Campaign $campaign
     * @return void
     */
    public function updateTotalsByTransactions(Campaign $campaign)
    {
        $campaign->recalcTotalsByTransactions();
        $campaign->getUser()->getCampaigns()->first()->recalcTotalsByTransactions();
        $this->repository->save();
    }

    /**
     * @param int $id
     * @return Campaign
     */
    public function findOrCreate(int $id): Campaign
    {
        $campaign = $this->repository->findById($id);
        if (!empty($campaign)) {
            return $campaign;
        }
        $campaign = new Campaign();
        $campaign->setUser($this->tokenStorage->getToken()->getUser());

        return $campaign;
    }

    /**
     * @param int $count
     * @return ArrayCollection|Campaign[]
     */
    public function getRandomActiveCampaigns(int $count)
    {
        return $this->repository->getRandomVisibleActiveCampaigns($count);
    }

    /**
     * @param \DateTime|null $date
     */
    public function handleStartedToday($date = null)
    {
        $today = $date ?? DateHelper::getUtcToday();
        $started = $this->repository->getStarted($today);

        $ids = array_map(function (Campaign $item) {
            return $item->getId();
        }, $started);
        LogHelper::info(
            "Started campaigns on %s: %s. Ids: %s.",
            $today->format('Y-m-d'),
            count($started),
            implode($ids, ', ')
        );

        foreach ($started as $campaign) {
            $event = new CampaignEvent($campaign);
            $this->dispatch(CampaignEvent::CAMPAIGN_STARTED, $event);
        }
    }

    /**
     * @param \DateTime|null $date
     */
    public function handleFinishedToday($date = null)
    {
        $today = $date ?? DateHelper::getUtcToday();
        $finished = $this->repository->getFinished($today);

        $ids = array_map(function (Campaign $item) {
            return $item->getId();
        }, $finished);
        if (!$ids) {
            return ;
        }
        LogHelper::info(
            "Finished campaigns on %s: %s. Ids: %s.",
            $today->format('Y-m-d'),
            count($finished),
            implode($ids, ', ')
        );

        foreach ($finished as $campaign) {
            if (!$campaign->isСollectedFullAmount()) {
                continue;
            }
            $cost = 0;

            $order = new Order();
            $order->setUser($campaign->getUser());
            $order->setCampaign($campaign);
            $order->setShipping($campaign->getEstimatedShipping());
            $order->setTax(0);
            $order->setTransactionFee(0);
            $order->setFmtFee(0);
            $order->setPrice(0);
            $order->setTotal(0);
            $order->setStatus(Order::STATUS_CART);
            $order = $this->cartManager->saveOrder($order);

            foreach ($campaign->getBooks() as $book) {
                if ($book->getStatus() != CampaignBook::STATUS_AVAILABLE) {
                    continue;
                }
                $bookOrder = new OrderItem();
                $bookOrder->setBook($book);
                $bookOrder->setQuantity(1);
                $bookOrder->setPrice($book->getPrice());
                $bookOrder->setSku($book->getSku());
                $bookOrder->setTitle($book->getTitle());
                $bookOrder->setOrder($order);
                $cost+=$book->getPrice();
                $book->setStatus(CampaignBook::STATUS_ORDERED);
                $order->addItem($bookOrder);
            }
            $order->setPrice($cost);
            $total = $cost + $campaign->getEstimatedShipping();
            $order->setTotal($total);

            $this->cartManager->sendOrder($order);
            $this->paymentManager->createTransaction($order);
//            if ($campaign->getDonationsFromPrevious()) {
//                $campaign->setFundedTotal($campaign->getFundedTotal() + $campaign->getDonationsFromPrevious() - $total);
//                $campaign->setDonationsFromPrevious(0);
//            } else {
//                $campaign->setFundedTotal($campaign->getFundedTotal() - $total);
//            }
            $campaign->setPurchasedTotal($campaign->getPurchasedTotal() + $total);
            $campaign->setFundedTotal($campaign->getFundedTotal() - $total);

            $this->repository->save();

            $event = new CampaignEvent($campaign);
            $this->dispatch(CampaignEvent::CAMPAIGN_FINISHED, $event);
        }
    }

    /**
     * @param Campaign $campaign
     * @return bool
     * @throws \Exception
     */
    protected function updateBooksInfo(Campaign $campaign)
    {
        foreach ($campaign->getBooks() as $book) {
            $this->bookManager->update($book);
        }

        return true;
    }

    /**
     * @return array
     */
    protected function getVisibilityData()
    {
        $visibility = [UserProfile::VISIBILITY_ALL];

        $token = $this->tokenStorage->getToken();
        if (null !== $token && $token->getUser() instanceof User) {
            $visibility[] = UserProfile::VISIBILITY_REGISTRED;
        }

        return $visibility;
    }

    /**
     * @return int
     */
    private function setDefaultShipping()
    {
        $shippingOptions = $this->shippingManager->getOptions();
        foreach ($shippingOptions as $option) {
            if($option->getId() == self::DEFAULT_SHIPPING_ID) {
                return $option->getId();
            }
        }

        return $shippingOptions[0]->getId();
    }
}
