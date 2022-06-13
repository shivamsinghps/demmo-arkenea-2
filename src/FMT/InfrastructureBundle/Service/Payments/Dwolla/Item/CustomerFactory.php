<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Exception\Exception;

/**
 * Class CustomerFactory
 */
class CustomerFactory
{
    /**
     * @param string|null $type
     * @param string|null $businessType
     *
     * @return string
     * @throws Exception
     */
    public static function getClass(?string $type, ?string $businessType = null): string
    {
        switch ($type) {
            case AbstractCustomer::TYPE_RECEIVE_ONLY:
                return ReceiveOnlyUser::class;
            case AbstractCustomer::TYPE_PERSONAL:
                return VerifiedPersonalCustomer::class;
            case AbstractCustomer::TYPE_BUSINESS:
                if ($businessType === VerifiedSoleProprietorshipCustomer::BUSINESS_TYPE_SOLE_PROPRIETORSHIP) {
                    return VerifiedSoleProprietorshipCustomer::class;
                }

                return VerifiedBusinessCustomer::class;
            case AbstractCustomer::TYPE_UNVERIFIED:
            case null:
                return UnverifiedCustomer::class;
        }

        throw new Exception(sprintf('Customer with type %s not exists', $type));
    }
}
