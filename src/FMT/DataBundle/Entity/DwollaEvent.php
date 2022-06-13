<?php

declare(strict_types=1);

namespace FMT\DataBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use FMT\DataBundle\Repository\DwollaEventRepository;

/**
 * @ORM\Entity(repositoryClass=DwollaEventRepository::class)
 * @ORM\Table(name="dwolla_event")
 */
class DwollaEvent implements EntityInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     */
    private $id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $received;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $topic;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $resourceId;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return DwollaEvent
     */
    public function setId(string $id): DwollaEvent
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     *
     * @return DwollaEvent
     */
    public function setCreated(DateTime $created): DwollaEvent
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getReceived(): DateTime
    {
        return $this->received;
    }

    /**
     * @param DateTime $received
     *
     * @return DwollaEvent
     */
    public function setReceived(DateTime $received): DwollaEvent
    {
        $this->received = $received;

        return $this;
    }

    /**
     * @return string
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * @param string $topic
     *
     * @return DwollaEvent
     */
    public function setTopic(string $topic): DwollaEvent
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    /**
     * @param string $resourceId
     *
     * @return DwollaEvent
     */
    public function setResourceId(string $resourceId): DwollaEvent
    {
        $this->resourceId = $resourceId;

        return $this;
    }
}
