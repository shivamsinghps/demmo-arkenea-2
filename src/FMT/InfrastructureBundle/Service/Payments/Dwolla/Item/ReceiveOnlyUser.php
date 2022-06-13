<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

/**
 * Class ReceiveOnlyUser
 */
class ReceiveOnlyUser extends AbstractCustomer
{
    /**
     * @var string|null
     */
    protected $businessName;

    public function __construct()
    {
        $this->type = self::TYPE_RECEIVE_ONLY;
    }

    /**
     * @return string|null
     */
    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    /**
     * @param string|null $businessName
     *
     * @return $this
     */
    public function setBusinessName(?string $businessName): self
    {
        $this->businessName = $businessName;

        return $this;
    }
}
