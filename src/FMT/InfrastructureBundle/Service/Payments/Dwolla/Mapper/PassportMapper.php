<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Passport;

/**
 * Class PassportMapper
 */
class PassportMapper extends AbstractMapper
{
    /**
     * @param Passport $source
     *
     * @return array
     */
    public function mapToArray(Passport $source): array
    {
        $result = [
            'country' => $source->getCountry(),
        ];

        if (!is_null($source->getNumber())) {
            $result['number'] = $source->getNumber();
        }

        return $result;
    }

    /**
     * @param array $source
     *
     * @return Passport
     */
    public function mapFromArray(array $source): Passport
    {
        $result = new Passport();
        $result
            ->setCountry($source['country'] ?? null)
            ->setNumber($source['number'] ?? null)
        ;

        return $result;
    }
}
