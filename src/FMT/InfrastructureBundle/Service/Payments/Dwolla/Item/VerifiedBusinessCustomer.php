<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\AddressTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\BusinessTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\PhoneTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\StateTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class VerifiedBusinessCustomer
 */
class VerifiedBusinessCustomer extends AbstractCustomer
{
    use AddressTrait;
    use StateTrait;
    use PhoneTrait;
    use BusinessTrait;

    public const BUSINESS_TYPE_CORPORATION = 'corporation';
    public const BUSINESS_TYPE_LLC = 'llc';
    public const BUSINESS_TYPE_PARTNERSHIP = 'partnership';

    /**
     * @var string
     *
     * @Assert\Choice({self::BUSINESS_TYPE_CORPORATION, self::BUSINESS_TYPE_LLC, self::BUSINESS_TYPE_PARTNERSHIP})
     */
    protected $businessType;

    /**
     * @var Controller|null
     *
     * @Assert\Valid
     */
    protected $controller;
    
    public function __construct()
    {
        $this->type = self::TYPE_BUSINESS;
    }

    /**
     * @param string $businessType
     *
     * @return $this
     */
    public function setBusinessType(string $businessType): self
    {
        $this->businessType = $businessType;

        return $this;
    }

    /**
     * @return Controller|null
     */
    public function getController(): ?Controller
    {
        return $this->controller;
    }

    /**
     * @param Controller|null $controller
     *
     * @return $this
     */
    public function setController(?Controller $controller): self
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return VerifiedBusinessCustomer
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
