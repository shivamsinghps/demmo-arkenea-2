<?php

namespace FMT\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserTransaction
 *
 * @ORM\Table(
 *     name="user_transaction",
 *     indexes={
 *          @ORM\Index(name="FK_transaction_sender", columns={"sender_id"}),
 *          @ORM\Index(name="FK_transaction_recipient", columns={"recipient_id"}),
 *          @ORM\Index(name="IX_transaction_type", columns={"type"}),
 *          @ORM\Index(name="FK_transaction_order", columns={"order_id"}),
 *          @ORM\Index(name="FK_transaction_campaign", columns={"campaign_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="FMT\DataBundle\Repository\UserTransactionRepository")
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class UserTransaction implements EntityInterface
{
    const TXN_DONATION = 1;
    const TXN_DIRECT_PURCHASE = 2;
    const TXN_BOOK_PURCHASE = 3;
    const TXN_BOOK_REFUND = 4;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     * })
     */
    private $sender;

    /**
     * @var \FMT\DataBundle\Entity\Campaign
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\Campaign", inversedBy="transactions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
     * })
     */
    private $campaign;

    /**
     * @var \FMT\DataBundle\Entity\Order
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\Order", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     * })
     */
    private $order;

    /**
     * @var \FMT\DataBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recipient_id", referencedColumnName="id")
     * })
     */
    private $recipient;

    /**
     * @var string
     *
     * @ORM\Column(name="external_id", type="string", length=50, nullable=true)
     */
    private $externalId;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer", nullable=false)
     * @deprecated This field should be removed from database and entity
     */
    private $amount;

    /**
     * @var integer
     *
     * @ORM\Column(name="fee", type="integer", nullable=false)
     */
    private $fee;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="payment_system_fee", type="integer", nullable=true)
     */
    private $paymentSystemFee;

    /**
     * @var integer
     *
     * @ORM\Column(name="net", type="integer", nullable=false)
     */
    private $net;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", nullable=true)
     */
    private $comment;

    /**
     * @var array
     *
     * @ORM\Column(name="unregistered_sender", type="json_array", nullable=true)
     */
    private $unregisteredSender;

    /**
     * @var string
     *
     * @ORM\Column(name="thanks", type="string", nullable=true)
     */
    private $thanks;

    /**
     * @var bool
     *
     * @ORM\Column(name="anonymous", type="smallint", nullable=false)
     */
    private $anonymous;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    public function __construct()
    {
        $this->date = new \DateTime("now");
        $this->externalId = uniqid("fmt-temp-", true);
        $this->anonymous = false;
        $this->amount = 0;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return MinimalUserInterface
     */
    public function getSender()
    {
        $sender = $this->sender;

        if (!$sender) {
            $sender = new UnregisteredUserDto($this->unregisteredSender);
        }

        return $sender;
    }

    /**
     * @param MinimalUserInterface $sender
     * @return $this
     */
    public function setSender(MinimalUserInterface $sender)
    {
        if ($sender instanceof UnregisteredUserDto) {
            $this->unregisteredSender = $sender->toArray();
        } elseif ($sender instanceof User) {
            $this->sender = $sender;
            $sender->addTransaction($this);
        }

        return $this;
    }

    /**
     * @return Campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * @param Campaign $campaign
     * @return $this
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return User
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param User $recipient
     * @return $this
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     * @return $this
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->net + $this->fee;
    }

    /**
     * @return int
     */
    public function getSpend()
    {
        return $this->net + $this->fee;
    }

    /**
     * @param int $amount
     * @return $this
     * @deprecated Calculable value
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setAmount($amount)
    {
        return $this;
    }

    /**
     * @return int
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * @param int $fee
     * @return $this
     */
    public function setFee($fee)
    {
        $this->fee = $fee;
        return $this;
    }

    /**
     * @return int
     */
    public function getNet()
    {
        return $this->net;
    }

    /**
     * @param int $net
     * @return $this
     */
    public function setNet($net)
    {
        $this->net = $net;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAnonymous()
    {
        return $this->anonymous;
    }

    /**
     * @param boolean $anonymous
     * @return $this
     */
    public function setAnonymous(bool $anonymous)
    {
        $this->anonymous = $anonymous;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int|null
     */
    public function getPaymentSystemFee()
    {
        return $this->paymentSystemFee;
    }

    /**
     * @param int|null $paymentSystemFee
     * @return UserTransaction
     */
    public function setPaymentSystemFee($paymentSystemFee): self
    {
        $this->paymentSystemFee = $paymentSystemFee;

        return $this;
    }

    /**
     * @param UserTransaction $transaction
     * @return bool
     */
    public function isEqualTo(UserTransaction $transaction)
    {
        return $this->externalId === $transaction->externalId;
    }

    /**
     * @return string|null
     */
    public function getThanks()
    {
        return $this->thanks;
    }

    /**
     * @param string $thanks
     * @return UserTransaction
     */
    public function setThanks($thanks): self
    {
        $this->thanks = $thanks;

        return $this;
    }

    public function getDonorFullName(): string
    {
        $sender = $this->getSender();
        $donorName = 'Anonymous';
        if (!$this->isAnonymous() && !empty($sender)) {
            $donorName = $sender->getFullName();
        }

        return $donorName;
    }

    public function isNeedThanks(): bool
    {
        return empty($this->getThanks());
    }

    /**
     * @return int
     */
    public function getSpendTotal()
    {
        if ($this->getType() === self::TXN_BOOK_PURCHASE) {
            return $this->getNet();
        }
        return $this->getAmount() + $this->getPaymentSystemFee();
    }
}
