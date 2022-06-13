<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use DateTime;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Address;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Controller;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Passport;

/**
 * Class ControllerMapper
 */
class ControllerMapper extends AbstractPersonMapper
{
    /**
     * @param Controller $source
     *
     * @return array
     */
    public function mapToArray(Controller $source): array
    {
        return parent::mapAbstractPersonToArray($source);
    }

    /**
     * @param array $source
     *
     * @return Controller
     */
    public function mapFromArray(array $source): Controller
    {
        $result = new Controller();
        $result
            ->setFirstName($source['firstName'])
            ->setLastName($source['lastName'])
            ->setTitle($source['title'])
            ->setSsn($source['ssn'] ?? null)
            ->setAddress($this->mapper->map($source['address'], Address::class))
        ;

        if (isset($source['dateOfBirth'])) {
            $result->setDateOfBirth(DateTime::createFromFormat(self::DATE_OF_BIRTH_FORMAT, $source['dateOfBirth']));
        }

        if (isset($source['passport'])) {
            $result->setPassport($this->mapper->map($source['passport'], Passport::class));
        }

        return $result;
    }
}
