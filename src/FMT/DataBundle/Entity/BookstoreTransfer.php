<?php

declare(strict_types=1);

namespace FMT\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FMT\DataBundle\Repository\BookstoreTransferRepository;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=BookstoreTransferRepository::class)
 * @ORM\Table(name="bookstore_transfer")
 */
class BookstoreTransfer implements EntityInterface
{
    use TimestampableEntity;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_REJECTED = 'rejected';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var BookstoreTransfer|null
     *
     * @ORM\ManyToOne(targetEntity=BookstoreTransfer::class, inversedBy="children")
     */
    private $parent;

    /**
     * @var BookstoreTransfer[]|Collection
     *
     * @ORM\OneToMany(targetEntity=BookstoreTransfer::class, mappedBy="parent")
     */
    private $children;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $net;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $status;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->status = self::STATUS_PENDING;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return BookstoreTransfer|null
     */
    public function getParent(): ?BookstoreTransfer
    {
        return $this->parent;
    }

    /**
     * @param BookstoreTransfer|null $parent
     *
     * @return BookstoreTransfer
     */
    public function setParent(?BookstoreTransfer $parent): BookstoreTransfer
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|BookstoreTransfer[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param Collection|BookstoreTransfer[] $children
     *
     * @return BookstoreTransfer
     */
    public function setChildren(Collection $children): BookstoreTransfer
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @param BookstoreTransfer $child
     *
     * @return BookstoreTransfer
     */
    public function addChild(BookstoreTransfer $child): BookstoreTransfer
    {
        if (!$this->children->contains($child) && $child !== $this) {
            $this->children->add($child);
            $child->parent = $this;
        }

        return $this;
    }

    /**
     * @param BookstoreTransfer $child
     *
     * @return BookstoreTransfer
     */
    public function removeChild(BookstoreTransfer $child): BookstoreTransfer
    {
        $this->children->removeElement($child);
        $child->setParent(null);

        return $this;
    }

    /**
     * @return int
     */
    public function getNet(): int
    {
        return $this->net;
    }

    /**
     * @param int $net
     *
     * @return BookstoreTransfer
     */
    public function setNet(int $net): BookstoreTransfer
    {
        $this->net = $net;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return BookstoreTransfer
     */
    public function setStatus(string $status): BookstoreTransfer
    {
        $this->status = $status;

        return $this;
    }
}
