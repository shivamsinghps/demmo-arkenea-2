<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use DateTime;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\VerifiedSoleProprietorshipCustomer;

/**
 * Class VerifiedSoleProprietorshipCustomerMapper
 */
class VerifiedSoleProprietorshipCustomerMapper extends AbstractCustomerMapper
{
    private const DATE_OF_BIRTH_FORMAT = 'Y-m-d';

    /**
     * @param VerifiedSoleProprietorshipCustomer $source
     *
     * @return array
     */
    public function mapToArray(VerifiedSoleProprietorshipCustomer $source): array
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
            'businessName' => $source->getBusinessName(),
            'businessType' => $source->getBusinessType(),
            'businessClassification' => $source->getBusinessClassification(),
        ]);

        if (!is_null($source->getAddress2())) {
            $result['address2'] = $source->getAddress2();
        }

        if (!is_null($source->getPhone())) {
            $result['phone'] = $source->getPhone();
        }

        if (!is_null($source->getDoingBusinessAs())) {
            $result['doingBusinessAs'] = $source->getDoingBusinessAs();
        }

        if (!is_null($source->getEin())) {
            $result['ein'] = $source->getEin();
        }

        if (!is_null($source->getWebsite())) {
            $result['website'] = $source->getWebsite();
        }

        return $result;
    }

    /**
     * @param array $source
     *
     * @return VerifiedSoleProprietorshipCustomer
     */
    public function mapFromArray(array $source): VerifiedSoleProprietorshipCustomer
    {
        /** @var VerifiedSoleProprietorshipCustomer $result */
        $result = $this->fillAbstractCustomerFromArray(new VerifiedSoleProprietorshipCustomer(), $source);
        $result
            ->setAddress1($source['address1'])
            ->setAddress2($source['address2'] ?? null)
            ->setCity($source['city'])
            ->setState($source['state'])
            ->setPostalCode($source['postalCode'])
            ->setPhone($source['phone'] ?? null)
            ->setBusinessName($source['businessName'])
            ->setDoingBusinessAs($source['doingBusinessAs'] ?? null)
            ->setBusinessClassification($source['businessClassification'])
            ->setEin($source['ein'] ?? null)
            ->setWebsite($source['website'] ?? null)
        ;

        if (isset($source['ssn'])) {
            $result->setSsn($source['ssn']);
        }

        if (isset($source['dateOfBirth'])) {
            $result->setDateOfBirth(DateTime::createFromFormat(self::DATE_OF_BIRTH_FORMAT, $source['dateOfBirth']));
        }

        return $result;
    }
}
