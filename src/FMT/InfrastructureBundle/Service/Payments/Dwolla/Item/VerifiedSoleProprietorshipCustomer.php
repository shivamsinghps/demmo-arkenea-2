<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\BusinessTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\DateOfBirthTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\PersonalTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\PhoneTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\StateTrait;

/**
 * Class VerifiedSoleProprietorshipCustomer
 */
class VerifiedSoleProprietorshipCustomer extends AbstractCustomer
{
    use PersonalTrait;
    use DateOfBirthTrait;
    use StateTrait;
    use PhoneTrait;
    use BusinessTrait;

    public const BUSINESS_TYPE_SOLE_PROPRIETORSHIP = 'soleProprietorship';

    public function __construct()
    {
        $this->type = self::TYPE_BUSINESS;
        $this->businessType = self::BUSINESS_TYPE_SOLE_PROPRIETORSHIP;
    }
}
