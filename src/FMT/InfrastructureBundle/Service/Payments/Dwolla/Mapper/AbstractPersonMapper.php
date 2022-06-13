<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\AbstractPerson;

/**
 * Class AbstractPersonMapper
 */
abstract class AbstractPersonMapper extends AbstractMapper
{
    protected const DATE_OF_BIRTH_FORMAT = 'Y-m-d';

    /**
     * @param AbstractPerson $source
     *
     * @return array
     */
    protected function mapAbstractPersonToArray(AbstractPerson $source): array
    {
        $result = [
            'firstName' => $source->getFirstName(),
            'lastName' => $source->getLastName(),
            'title' => $source->getTitle(),
            'dateOfBirth' => $source->getDateOfBirth()->format(self::DATE_OF_BIRTH_FORMAT),
            'address' => $this->mapper->map($source->getAddress(), 'array'),
        ];

        if (!is_null($source->getSsn())) {
            $result['ssn'] = $source->getSsn();
        }

        if (!is_null($source->getPassport())) {
            $result['passport'] = $this->mapper->map($source->getPassport(), 'array');
        }

        return $result;
    }
}
