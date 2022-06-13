<?php

declare(strict_types=1);

namespace FMT\DataBundle\Entity\Logs;

use Doctrine\ORM\Mapping as ORM;
use FMT\DataBundle\Entity\OrderItem;
use Gedmo\Loggable\Entity\LogEntry;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="log_order_item")
 * @ORM\Entity
 */
class LogOrderItem extends LogEntry
{
    /**
     * @var OrderItem
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\OrderItem", inversedBy="logs")
     */
    private $orderItem;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Choice(OrderItem::ALL_STATUSES)
     */
    private $status = null;

    /**
     * @return OrderItem
     */
    public function getOrderItem(): OrderItem
    {
        return $this->orderItem;
    }

    /**
     * @param OrderItem $orderItem
     */
    public function setOrderItem(OrderItem $orderItem): void
    {
        $this->orderItem = $orderItem;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     *
     * @return $this
     */
    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
