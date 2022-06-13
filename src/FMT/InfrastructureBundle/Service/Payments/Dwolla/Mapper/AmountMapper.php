<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Exception\InvalidCurrencyException;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Amount;

/**
 * Class AmountMapper
 */
class AmountMapper extends AbstractMapper
{
    /**
     * @param Amount $source
     *
     * @return array
     * @throws InvalidCurrencyException
     */
    public function mapToArray(Amount $source): array
    {
        if ($source->getCurrency() !== Amount::CURRENCY_USD) {
            throw new InvalidCurrencyException();
        }

        return [
            'value'    => $this->fromIntPrice($source->getValue()),
            'currency' => $source->getCurrency(),
        ];
    }

    /**
     * @param array $source
     *
     * @return Amount
     * @throws InvalidCurrencyException
     */
    public function mapFromArray(array $source): Amount
    {
        if ($source['currency'] !== Amount::CURRENCY_USD) {
            throw new InvalidCurrencyException();
        }
        
        return new Amount($this->toIntPrice($source['value']), $source['currency']);
    }
}
