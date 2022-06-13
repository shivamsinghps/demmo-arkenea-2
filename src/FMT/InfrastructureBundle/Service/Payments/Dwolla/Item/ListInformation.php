<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

/**
 * Class DwollaList
 */
class ListInformation
{
    /**
     * @var int
     */
    protected $total;

    /**
     * @var string
     */
    protected $selfLink;

    /**
     * @var string|null
     */
    protected $firstLink;

    /**
     * @var string|null
     */
    protected $lastLink;

    /**
     * @var string|null
     */
    protected $nextLink;

    /**
     * @param int $total
     */
    public function __construct(int $total = 0)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     *
     * @return ListInformation
     */
    public function setTotal(int $total): ListInformation
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelfLink(): string
    {
        return $this->selfLink;
    }

    /**
     * @param string $selfLink
     *
     * @return ListInformation
     */
    public function setSelfLink(string $selfLink): ListInformation
    {
        $this->selfLink = $selfLink;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstLink(): ?string
    {
        return $this->firstLink;
    }

    /**
     * @param string|null $firstLink
     *
     * @return ListInformation
     */
    public function setFirstLink(?string $firstLink): ListInformation
    {
        $this->firstLink = $firstLink;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastLink(): ?string
    {
        return $this->lastLink;
    }

    /**
     * @param string|null $lastLink
     *
     * @return ListInformation
     */
    public function setLastLink(?string $lastLink): ListInformation
    {
        $this->lastLink = $lastLink;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNextLink(): ?string
    {
        return $this->nextLink;
    }

    /**
     * @param string|null $nextLink
     *
     * @return ListInformation
     */
    public function setNextLink(?string $nextLink): ListInformation
    {
        $this->nextLink = $nextLink;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLast(): bool
    {
        return is_null($this->getLastLink()) || $this->getLastLink() === $this->getSelfLink();
    }
}
