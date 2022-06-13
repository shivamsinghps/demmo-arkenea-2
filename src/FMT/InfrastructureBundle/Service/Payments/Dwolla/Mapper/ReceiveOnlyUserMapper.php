<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;


use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\ReceiveOnlyUser;

/**
 * Class ReceiveOnlyUserMapper
 */
class ReceiveOnlyUserMapper extends AbstractCustomerMapper
{
    /**
     * @param ReceiveOnlyUser $source
     *
     * @return array
     */
    public function mapToArray(ReceiveOnlyUser $source): array
    {
        $result = $this->mapFromAbstractCustomerToArray($source);
        $result['type'] = $source->getType();

        if (!is_null($source->getBusinessName())) {
            $result['businessName'] = $source->getBusinessName();
        }

        return $result;
    }

    /**
     * @param array $source
     *
     * @return ReceiveOnlyUser
     */
    public function mapFromArray(array $source): ReceiveOnlyUser
    {
        /** @var ReceiveOnlyUser $result */
        $result = $this->fillAbstractCustomerFromArray(new ReceiveOnlyUser(), $source);
        $result->setBusinessName($source['businessName'] ?? null);

        return $result;
    }
}
