<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Controller;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\VerifiedBusinessCustomer;

/**
 * Class VerifiedBusinessCustomerMapper
 */
class VerifiedBusinessCustomerMapper extends AbstractCustomerMapper
{
    /**
     * @param VerifiedBusinessCustomer $source
     *
     * @return array
     */
    public function mapToArray(VerifiedBusinessCustomer $source): array
    {
        $result = $this->mapFromAbstractCustomerToArray($source);

        $result = array_merge($result, [
            'type' => $source->getType(),
            'address1' => $source->getAddress1(),
            'city' => $source->getCity(),
            'state' => $source->getState(),
            'postalCode' => $source->getPostalCode(),
            'businessName' => $source->getBusinessName(),
            'businessType' => $source->getBusinessType(),
            'businessClassification' => $source->getBusinessClassification(),
            'ein' => $source->getEin(),
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

        if (!is_null($source->getController())) {
            $result['controller'] = $this->mapper->map($source->getController(), 'array');
        }

        return $result;
    }

    /**
     * @param array $source
     *
     * @return VerifiedBusinessCustomer
     */
    public function mapFromArray(array $source): VerifiedBusinessCustomer
    {
        /** @var VerifiedBusinessCustomer $result */
        $result = $this->fillAbstractCustomerFromArray(new VerifiedBusinessCustomer(), $source);
        $result
            ->setAddress1($source['address1'])
            ->setAddress2($source['address2'] ?? null)
            ->setCity($source['city'])
            ->setType($source['type'])
            ->setState($source['state'])
            ->setPostalCode($source['postalCode'])
            ->setPhone($source['phone'] ?? null)
            ->setBusinessName($source['businessName'])
            ->setBusinessType($source['businessType'])
            ->setDoingBusinessAs($source['doingBusinessAs'] ?? null)
            ->setBusinessClassification($source['businessClassification'])
            ->setEin($source['ein'] ?? null)
            ->setWebsite($source['website'] ?? null)
        ;

        if (isset($source['controller'])) {
            $result->setController($this->mapper->map($source['controller'], Controller::class));
        }

        return $result;
    }
}
