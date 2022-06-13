<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\DateOfBirthTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\PersonalTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\PhoneTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\StateTrait;

/**
 * Class VerifiedPersonalCustomer
 */
class VerifiedPersonalCustomer extends AbstractCustomer
{
    use PersonalTrait;
    use DateOfBirthTrait;
    use StateTrait;
    use PhoneTrait;

    public function __construct()
    {
        $this->type = self::TYPE_PERSONAL;
    }

    public function getType(): string
    {
        return parent::getType();
    }
}
