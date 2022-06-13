<?php

namespace FMT\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Order
 *
 * @ORM\Table(
 *      name="`order`",
 *      indexes={
 *          @ORM\Index(name="FK_order_user", columns={"user_id"}),
 *          @ORM\Index(name="FK_order_address", columns={"address_id"}),
 *          @ORM\Index(name="FK_order_campaign", columns={"campaign_id"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="FMT\DataBundle\Repository\OrderRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Order implements EntityInterface
{
    use TimestampableEntity;

    const STATUS_CART = 'cart';
    const STATUS_COMPLETED = 'completed';

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
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \FMT\DataBundle\Entity\Campaign
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\Campaign")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
     * })
     */
    private $campaign;

    /**
     * @var \FMT\DataBundle\Entity\Address
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\Address")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     * })
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="external_id", type="string", length=50, nullable=true)
     */
    private $externalId;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="integer", nullable=false)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="shipping", type="integer", nullable=false)
     */
    private $shipping;

    /**
     * @var integer
     *
     * @ORM\Column(name="tax", type="integer", nullable=false)
     */
    private $tax;

    /**
     * @var integer
     *
     * @ORM\Column(name="transaction_fee", type="integer", nullable=false)
     */
    private $transactionFee;

    /**
     * @var integer
     *
     * @ORM\Column(name="fmt_fee", type="integer", nullable=false)
     */
    private $fmtFee;

    /**
     * @var integer
     *
     * @ORM\Column(name="total", type="integer", nullable=false)
     */
    private $total;

    /**
     * @var string
     *
     * @Assert\Choice({
     *     Order::STATUS_CART,
     *     Order::STATUS_COMPLETED
     * })
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="submitted", type="datetime", nullable=true)
     */
    private $submitted;

    /**
     * @var OrderItem[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="FMT\DataBundle\Entity\OrderItem",
     *     mappedBy="order",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     * )
     */
    private $items;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $anonymousToken;

    /**
     * @var integer
     *
     * @ORM\Column(name="unprocessed_amount", type="integer", nullable=false)
     */
    private $unprocessedAmount = 0;

    /**
     * Campaign constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
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
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @param int $shipping
     * @return $this
     */
    public function setShipping($shipping)
    {
        $this->shipping = $shipping;
        return $this;
    }

    /**
     * @return int
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param int $tax
     * @return $this
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
        return $this;
    }

    /**
     * @return int
     */
    public function getTransactionFee()
    {
        return $this->transactionFee;
    }

    /**
     * @param int $transactionFee
     * @return $this
     */
    public function setTransactionFee($transactionFee)
    {
        $this->transactionFee = $transactionFee;
        return $this;
    }

    /**
     * @return int
     */
    public function getFmtFee()
    {
        return $this->fmtFee;
    }

    /**
     * @param int $fmtFee
     * @return $this
     */
    public function setFmtFee($fmtFee)
    {
        $this->fmtFee = $fmtFee;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     * @return $this
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSubmitted()
    {
        return $this->submitted;
    }

    /**
     * @return OrderItem[]|ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param OrderItem $item
     * @return $this
     */
    public function addItem(OrderItem $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);

            $item->setOrder($this);
        }

        return $this;
    }

    /**
     * @param OrderItem $item
     * @return $this
     */
    public function removeItem(OrderItem $item)
    {
        $this->items->removeElement($item);

        return $this;
    }

    /**
     * @return string
     */
    public function getAnonymousToken()
    {
        return $this->anonymousToken;
    }

    /**
     * @param string $anonymousToken
     * @return $this
     */
    public function setAnonymousToken($anonymousToken)
    {
        $this->anonymousToken = $anonymousToken;

        return $this;
    }

    /**
     * @return int
     */
    public function getUnprocessedAmount()
    {
        return $this->unprocessedAmount;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setUnprocessedAmount($unprocessedAmount)
    {
        $this->unprocessedAmount = $unprocessedAmount;

        return $this;
    }

    /**
     * @return $this
     */
    public function setZeroCartPrices()
    {
        $this
            ->setPrice(0)
            ->setShipping(0)
            ->setTax(0)
            ->setTransactionFee(0)
            ->setFmtFee(0)
            ->setTotal(0)
        ;

        return $this;
    }

    #region Price calculation

    /**
     * Total amount of shopping cart for checkout
     *
     * @return int
     */
    public function getTotalForCheckout()
    {
        return $this->price + $this->shipping + $this->tax;
    }

    /**
     * @return $this
     */
    public function recalculateTotal()
    {
        $this->setTotal(
            $this->getTotalForCheckout() + $this->fmtFee + $this->transactionFee
        );

        return $this;
    }

    /**
     * Sets all prices to 0
     */
    public function resetPrice()
    {
        $this->setPrice(0)
            ->setTax(0)
            ->setShipping(0)
            ->setTax(0)
            ->setTransactionFee(0)
            ->setFmtFee(0)
            ->setTotal(0);
    }

    #endregion

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status == Order::STATUS_COMPLETED;
    }
}
