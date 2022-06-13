<?php

namespace FMT\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FMT\DataBundle\Entity\Logs\LogOrderItem;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OrderItem
 *
 * @ORM\Table(
 *      name="order_item",
 *      indexes={
 *          @ORM\Index(name="FK_item_order", columns={"order_id"}),
 *          @ORM\Index(name="FK_item_book", columns={"book_id"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="FMT\DataBundle\Repository\OrderItemRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Gedmo\Loggable(logEntryClass="FMT\DataBundle\Entity\Logs\LogOrderItem")
 */
class OrderItem implements EntityInterface
{
    use TimestampableEntity;

    const STATUS_CART = 'cart';
    const STATUS_PURCHASED = 'purchased';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_PRE_RETURNED = 'pre_returned';
    const STATUS_RETURNED = 'returned';

    public const ALL_STATUSES = [
        self::STATUS_CART,
        self::STATUS_PURCHASED,
        self::STATUS_SUBMITTED,
        self::STATUS_SHIPPED,
        self::STATUS_RETURNED,
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \FMT\DataBundle\Entity\Order
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\Order", inversedBy="items")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     * })
     */
    private $order;

    /**
     * @var \FMT\DataBundle\Entity\CampaignBook
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\CampaignBook")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="book_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $book;

    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=255, nullable=false)
     */
    private $sku;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="integer", nullable=false)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var integer
     *
     * @Assert\Choice(OrderItem::ALL_STATUSES)
     * @ORM\Column(name="status", type="string", nullable=false)
     *
     * @Gedmo\Versioned
     */
    private $status;

    /**
     * @var LogOrderItem[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="FMT\DataBundle\Entity\Logs\LogOrderItem",
     *     mappedBy="orderItem",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     * )
     */
    private $logs;

    /**
     * @var integer
     *
     * @ORM\Column(name="unprocessed_amount", type="integer", nullable=false)
     */
    private $unprocessedAmount = 0;

    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return CampaignBook
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * @param CampaignBook $book
     *
     * @return $this
     */
    public function setBook($book)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     *
     * @return $this
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     *
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
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

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
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * @return ArrayCollection|LogOrderItem[]
     */
    public function getLogs()
    {
        return $this->logs;
    }

    public function addLog(LogOrderItem $logOrderItem): self
    {
        if (!$this->logs->contains($logOrderItem)) {
            $this->logs->add($logOrderItem);
            $logOrderItem->setOrderItem($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeLogs()
    {
        foreach ($this->logs as $log) {
            $this->logs->removeElement($log);
        }

        return $this;
    }
}
