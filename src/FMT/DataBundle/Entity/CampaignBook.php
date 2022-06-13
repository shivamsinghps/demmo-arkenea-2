<?php

namespace FMT\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FMT\DataBundle\Traits\EnumTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * CampaignBook
 *
 * @ORM\Table(
 *     name="campaign_book",
 *     indexes={
 *          @ORM\Index(name="IX_book_status", columns={"status"}),
 *          @ORM\Index(name="IX_book_campaign", columns={"campaign_id"})
 *     }
 * )
 * @ORM\Entity
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"productFamilyId", "sku", "campaignId"})
 */
class CampaignBook implements EntityInterface, CampaignProductInterface
{
    use TimestampableEntity;
    use EnumTrait;

    // TODO: Combine with ProductInterface constants
    const STATUS_AVAILABLE = 0;
    const STATUS_OUT_OF_STOCK = 1;
    const STATUS_ORDERED = 2;
    const STATUS_UNAVAILABLE = 3;
    const STATUS_RETURNED = 4;
    // TODO: added new status? Check messages.en.yml!

    const STATE_UNKNOWN = 0;
    const STATE_NEW = 1;
    const STATE_USED = 2;
    // TODO: added new state? Check messages.en.yml!

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \FMT\DataBundle\Entity\Campaign
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\Campaign", inversedBy="books")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
     * })
     */
    private $campaign;

    /**
     * @var string
     *
     * @ORM\Column(name="product_family_id", type="string", length=255, nullable=false)
     */
    private $productFamilyId;

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
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255, nullable=true)
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="isbn", type="string", length=15, nullable=true)
     */
    private $isbn;

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
    private $quantity = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    private $status = self::STATUS_AVAILABLE;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="smallint", nullable=false)
     */
    private $state = self::STATE_UNKNOWN;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Campaign
     */
    public function getCampaign(): Campaign
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
     * @return string
     */
    public function getProductFamilyId()
    {
        return $this->productFamilyId;
    }

    /**
     * @param string $productFamilyId
     * @return $this
     */
    public function setProductFamilyId($productFamilyId)
    {
        $this->productFamilyId = $productFamilyId;
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
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     * @return $this
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;
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
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return array_search($this->status, self::getAllowedStatuses());
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return string
     */
    public function getStateName()
    {
        if (!is_numeric($this->state)) {
            return sprintf('STATE_%s', strtoupper($this->state));
        }

        return array_search($this->state, self::getConstants('STATE_'));
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->status == CampaignBook::STATUS_AVAILABLE;
    }

    /**
     * @return bool
     */
    public function isOrdered()
    {
        return $this->status == CampaignBook::STATUS_ORDERED;
    }
}
