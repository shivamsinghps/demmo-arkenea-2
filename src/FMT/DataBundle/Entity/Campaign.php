<?php

namespace FMT\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FMT\DataBundle\Traits\EnumTrait;
use FMT\InfrastructureBundle\Helper\CurrencyHelper;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Campaign
 *
 * @ORM\Table(
 *     name="campaign",
 *     indexes={
 *          @ORM\Index(name="IX_campaign_status", columns={"status"}),
 *          @ORM\Index(name="FK_campaign_user", columns={"user_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="FMT\DataBundle\Repository\CampaignRepository")
 * @ORM\HasLifecycleCallbacks()
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Campaign implements EntityInterface
{
    use TimestampableEntity;
    use EnumTrait;

    // @deprecated, but use as an example with todo-text:
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_PAUSED = 2;
    // TODO: added new status? Check messages.en.yml!

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \FMT\DataBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\User", inversedBy="campaigns")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=false)
     */
    private $endDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="shipping_option", type="integer", options={"default": 0})
     */
    private $shippingOption;

    /**
     * @var integer
     *
     * @ORM\Column(name="estimated_shipping", type="integer", nullable=true)
     */
    private $estimatedShipping;

    /**
     * @var integer
     * TODO is used?
     *
     * @ORM\Column(name="estimated_tax", type="integer", nullable=true)
     */
    private $estimatedTax;

    /**
     * @var integer
     *
     * @ORM\Column(name="estimated_cost", type="integer", nullable=true)
     */
    private $estimatedCost;

    /**
     * @var integer
     *
     * @ORM\Column(name="funded_total", type="integer", nullable=false)
     */
    private $fundedTotal = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchased_total", type="integer", nullable=false, options={"default": 0})
     */
    private $purchasedTotal = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="donations_from_previous", type="integer", nullable=false, options={"default": 0})
     */
    private $donationsFromPrevious = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_paused", type="smallint", nullable=false, options={"default": 0})
     */
    private $isPaused = false;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="paused_at", type="datetime", nullable=true)
     */
    private $pausedAt;

    /**
     * @var CampaignBook[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CampaignBook", mappedBy="campaign", orphanRemoval=true, cascade={"persist"})
     */
    private $books;

    /**
     * @var CampaignContact[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CampaignContact", mappedBy="campaign", cascade={"persist"})
     */
    private $contacts;

    /**
     * @var Order[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Order", mappedBy="campaign", orphanRemoval=true, cascade={"persist"})
     */
    private $orders;

    /**
     * @var UserTransaction[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FMT\DataBundle\Entity\UserTransaction", mappedBy="campaign")
     */
    private $transactions;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mass_mailing_called", type="smallint", nullable=false, options={"default": 0})
     */
    private $massMailingCalled = false;

    /**
     * Campaign constructor.
     */
    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return $this
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     * @return $this
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getShippingOption()
    {
        return $this->shippingOption;
    }

    /**
     * @param int $shippingOption
     * @return $this
     */
    public function setShippingOption($shippingOption)
    {
        $this->shippingOption = $shippingOption;
        return $this;
    }

    /**
     * @return string
     */
    public function getEstimatedShippingPrice()
    {
        return CurrencyHelper::priceFilter($this->estimatedShipping);
    }

    /**
     * @return int
     */
    public function getEstimatedShipping()
    {
        return $this->estimatedShipping;
    }

    /**
     * @param int $estimatedShipping
     * @return $this
     */
    public function setEstimatedShipping($estimatedShipping)
    {
        $this->estimatedShipping = $estimatedShipping;
        return $this;
    }

    /**
     * @return string
     */
    public function getEstimatedTaxPrice()
    {
        return CurrencyHelper::priceFilter($this->estimatedTax);
    }

    /**
     * @return int
     */
    public function getEstimatedTax()
    {
        return $this->estimatedTax;
    }

    /**
     * @param int $estimatedTax
     * @return $this
     */
    public function setEstimatedTax($estimatedTax)
    {
        $this->estimatedTax = $estimatedTax;
        return $this;
    }

    /**
     * @return string
     */
    public function getEstimatedCostPrice()
    {
        return CurrencyHelper::priceFilter($this->estimatedCost);
    }

    /**
     * @return int
     */
    public function getEstimatedCost()
    {
        return $this->estimatedCost;
    }

    /**
     * @param int $estimatedCost
     * @return $this
     */
    public function setEstimatedCost($estimatedCost)
    {
        $this->estimatedCost = $estimatedCost;
        return $this;
    }

    /**
     * @return int
     */
    public function getFundedTotal()
    {
        return $this->fundedTotal;
    }

    /**
     * @return string
     */
    public function getFundedTotalPrice()
    {
        return CurrencyHelper::priceFilter($this->getFundedTotal());
    }

    /**
     * @param int $fundedTotal
     * @return $this
     */
    public function setFundedTotal($fundedTotal)
    {
        $this->fundedTotal = $fundedTotal;
        return $this;
    }

    /**
     * @deprecated
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @deprecated
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPaused()
    {
        return $this->isPaused;
    }

    /**
     * @param bool $boolean
     * @return $this
     */
    public function setPaused(bool $boolean)
    {
        $this->isPaused = $boolean;

        return $this;
    }

    /**
     * @return $this
     */
    public function togglePaused()
    {
        $this->isPaused = !$this->isPaused;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getPausedAt()
    {
        return $this->pausedAt;
    }

    /**
     * @param \DateTime|null $time
     * @return $this
     */
    public function setPausedAt(\DateTime $time = null)
    {
        $this->pausedAt = $time;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInactive()
    {
        return !$this->isActive();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        $result = false;

        if ($this->startDate && $this->endDate) {
            $now = new \DateTime();
            $now->setTime(0, 0);

            $result = $this->startDate <= $now && $this->endDate >= $now;
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        $result = false;

        if ($this->startDate) {
            $now = new \DateTime();
            $now->setTime(0, 0);

            $result = $this->startDate <= $now;
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        $result = false;

        if (!$this->startDate || !$this->endDate) {
            return $result;
        }

        $now = new \DateTime();
        $now->setTime(0, 0);
        if ($this->endDate < $now) {
            $result = true;
        }

        return $result;
    }

    /**
     * @return CampaignBook[]|ArrayCollection
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * @param ArrayCollection $books
     * @return $this
     */
    public function setBooks(ArrayCollection $books)
    {
        $this->books = $books;

        return $this;
    }

    /**
     * @param CampaignBook $book
     * @return $this
     */
    public function addBook(CampaignBook $book)
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);

            $book->setCampaign($this);
        }

        return $this;
    }

    /**
     * @param CampaignBook $book
     * @return $this
     */
    public function removeBook(CampaignBook $book)
    {
        $this->books->removeElement($book);

        return $this;
    }

    /**
     * @return ArrayCollection|CampaignContact[]
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @param UserContact $contact
     * @return CampaignContact|null
     */
    public function findContact(UserContact $contact)
    {
        $id = $contact->getId();
        $existing = $this->contacts->filter(function (CampaignContact $item) use ($id) {
            return $item->getContact() && $item->getContact()->getId() === $id;
        });

        return $existing->isEmpty() ? null : $existing->first();
    }

    /**
     * @param UserContact $contact
     * @return bool
     */
    public function hasContact(UserContact $contact)
    {
        return $this->findContact($contact) !== null;
    }

    /**
     * @param UserContact $contact
     * @return CampaignContact
     */
    public function addContact(UserContact $contact)
    {
        $result = new CampaignContact();
        $result->setCampaign($this);
        $result->setContact($contact);
        $result->setStatus(CampaignContact::STATUS_UNCONFIRMED);

        $this->contacts->add($result);

        return $result;
    }

    /**
     * @return Order[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return float
     */
    public function getOrdersTotal()
    {
        return array_sum(
            array_map(
                function (Order $order) {
                    return $order->isCompleted() ? $order->getTotal() : 0;
                },
                $this->orders->getValues()
            )
        );
    }

    /**
     * @return float
     */
    public function getOrdersPrice()
    {
        return array_sum(
            array_map(
                function (Order $order) {
                    return $order->isCompleted() ? $order->getPrice() : 0;
                },
                $this->orders->getValues()
            )
        );
    }

    /**
     * @return int
     */
    public function daysLeft()
    {
        $days = 0;
        $now = new \DateTime();

        if ($this->startDate > $now) {
            $start = clone $this->startDate;
            $days = $start->diff($now)->days;
        }

        return $days;
    }

    /**
     * @return float
     */
    public function getPercentOfFunded()
    {
        $campaignGoal = $this->getCampaignGoal();
        if ($campaignGoal) {
            return round(floor(100*($this->getFundedTotal() + $this->getPurchasedTotal()) / $campaignGoal) / 100, 2);
        } else {
            return 0;
        }
    }

    /**
     * @return float
     */
    public function isСollectedFullAmount()
    {
        return $this->getCampaignGoal() <= $this->getFundedTotal() + $this->getPurchasedTotal() + $this->getDonationsFromPrevious();
    }

    /**
     * @return UserMajor|null
     */
    public function getMajor()
    {
        return $this->user ? $this->user->getMajor() : null;
    }

    /**
     * @return int
     */
    public function getCampaignGoal()
    {
        return $this->estimatedShipping + $this->estimatedCost;
    }

    /**
     * @return ArrayCollection|UserTransaction[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @param ArrayCollection|UserTransaction[] $transactions
     * @return Campaign
     */
    public function setTransactions($transactions)
    {
        $this->transactions = $transactions;
        return $this;
    }

    /**
     * @return int
     */
    public function getPurchasedTotal(): int
    {
        return $this->purchasedTotal;
    }

    /**
     * @return string
     */
    public function getPurchasedTotalPrice()
    {
        return CurrencyHelper::priceFilter($this->getPurchasedTotal());
    }

    /**
     * @param int $purchasedTotal
     * @return Campaign
     */
    public function setPurchasedTotal(int $purchasedTotal): self
    {
        $this->purchasedTotal = $purchasedTotal;

        return $this;
    }

    /**
     * @return int
     */
    public function getDonationsFromPrevious(): int
    {
        return $this->donationsFromPrevious;
    }

    /**
     * @param int $donationsFromPrevious
     * @return Campaign
     */
    public function setDonationsFromPrevious(int $donationsFromPrevious): Campaign
    {
        $this->donationsFromPrevious = $donationsFromPrevious;

        return $this;
    }

    /**
     * @return int
     */
    public function getAllowedDonateAmount(): int
    {
        return max($this->getCampaignGoal() - $this->getFundedTotal() - $this->getPurchasedTotal(), 0);
    }

    /**
     * @return string
     */
    public function getAllowedDonateAmountPrice()
    {
        return CurrencyHelper::priceFilter($this->getAllowedDonateAmount());
    }

    /**
     * @return void
     */
    public function recalcTotalsByTransactions()
    {
        $funded = $this->getDonationsFromPrevious();
        $purchased = 0;
        foreach ($this->getTransactions() as $transaction) {
            $transactionNet = $transaction->getNet();
            switch ($transaction->getType()) {
                case UserTransaction::TXN_DONATION:
                case UserTransaction::TXN_BOOK_REFUND:
                case UserTransaction::TXN_DIRECT_PURCHASE:
                    $funded += $transactionNet;
                    break;
                case UserTransaction::TXN_BOOK_PURCHASE:
                    break;
            }
        }
        foreach ($this->getBooks() as $book) {
            switch ($book->getStatus()) {
                case CampaignBook::STATUS_AVAILABLE:
                    break;
                case CampaignBook::STATUS_RETURNED:
                case CampaignBook::STATUS_ORDERED:
                case CampaignBook::STATUS_OUT_OF_STOCK:
                    $purchased += $book->getPrice();
                    break;
            }
        }
        if ($purchased == $this->estimatedCost) {
            $this->setEstimatedShipping(0);
        }
        $this->setFundedTotal($funded);
        $this->setPurchasedTotal($purchased);
    }

    /**
     * @return bool
     */
    public function isPositiveBalance()
    {
        return $this->fundedTotal > 0;
    }

    /**
     * @return bool
     */
    public function isMassMailingCalled()
    {
        return $this->massMailingCalled;
    }

    /**
     * @param bool $massMailedCalled
     * @return $this
     */
    public function setMassMailedCalled(bool $massMailingCalled)
    {
        $this->massMailingCalled = $massMailingCalled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMassMailedAvailable(){
        $now = new \DateTime();
        $now->setTime(0, 0);

        if ($this->startDate > $now ) {
            $daysToStart = date_diff($this->startDate, $now)->days;
            return $daysToStart <= 15 && !$this->isMassMailingCalled() ? true : false;
        }

        return !$this->isMassMailingCalled() && !$this->isStarted();
    }
}
