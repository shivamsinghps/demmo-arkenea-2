<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use DateTime;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\VerifiedPersonalCustomer;

/**
 * Class VerifiedPersonalCustomerMapper
 */
class VerifiedPersonalCustomerMapper extends AbstractCustomerMapper
{
    private const DATE_OF_BIRTH_FORMAT = 'Y-m-d';

    /**
     * @param VerifiedPersonalCustomer $source
     *
     * @return array
     */
    public function mapToArray(VerifiedPersonalCustomer $source): array
    {
        $result = $this->mapFromAbstractCustomerToArray($source);

        $result = array_merge($result, [
            'type' => $source->getType(),
            'address1' => $source->getAddress1(),
            'city' => $source->getCity(),
            'state' => $source->getState(),
            'postalCode' => $source->getPostalCode(),
            'dateOfBirth' => $source->getDateOfBirth()->format(self::DATE_OF_BIRTH_FORMAT),
            'ssn' => $source->getSsn(),
        ]);

        if (!is_null($source->getAddress2())) {
            $result['address2'] = $source->getAddress2();
        }

        if (!is_null($source->getPhone())) {
            $result['phone'] = $source->getPhone();
        }

        return $result;
    }

    /**
     * @param array $source
     *
     * @return VerifiedPersonalCustomer
     */
    public function mapFromArray(array $source): VerifiedPersonalCustomer
    {
        /** @var VerifiedPersonalCustomer $result */
        $result = $this->fillAbstractCustomerFromArray(new VerifiedPersonalCustomer(), $source);
        $result
            ->setAddress1($source['address1'])
            ->setAddress2($source['address2'] ?? null)
            ->setCity($source['city'])
            ->setState($source['state'])
            ->setPostalCode($source['postalCode'])
            ->setDateOfBirth(DateTime::createFromFormat(self::DATE_OF_BIRTH_FORMAT, $source['dateOfBirth']))
            ->setSsn($source['ssn'])
            ->setPhone($source['phone'] ?? null)
        ;

        return $result;
    }
}
