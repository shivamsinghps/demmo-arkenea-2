<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\BeneficialOwner;

/**
 * Class BeneficialOwnerMapper
 */
class BeneficialOwnerMapper extends AbstractPersonMapper
{
    /**
     * @param BeneficialOwner $source
     *
     * @return array
     */
    public function mapToArray(BeneficialOwner $source): array
    {
        return parent::mapAbstractPersonToArray($source);
    }
}
