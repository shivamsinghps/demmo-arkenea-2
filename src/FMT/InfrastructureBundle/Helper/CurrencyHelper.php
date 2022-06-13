<?php

namespace FMT\InfrastructureBundle\Helper;

/**
 * Class CurrencyHelper
 * @package FMT\InfrastructureBundle\Helper
 */
class CurrencyHelper
{
    /**
     * @param $cents
     * @param int $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     * @return string
     */
    public static function priceFilter($cents = 0, $decimals = null, $decPoint = '.', $thousandsSep = '')
    {
        $price = self::numberFilter($cents / 100, $decimals, $decPoint, $thousandsSep);
        $price = '$' . $price;

        return $price;
    }

    /**
     * @param $number
     * @param int $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     * @return string
     */
    public static function numberFilter($number, $decimals = null, $decPoint = '.', $thousandsSep = '')
    {
        if (is_null($decimals) || !is_int($decimals)) {
            $decimals = 2;
        }

        $number = round($number, $decimals) == 0 ? 0 : $number;
        $formattedNumber = ((floor($number) == round($number, $decimals)) ?
            number_format($number, null, $decPoint, $thousandsSep) :
            number_format($number, $decimals, $decPoint, $thousandsSep));

        return $formattedNumber;
    }

    /**
     * @param $number
     * @param bool $fractional
     * @return string
     */
    public static function percentFilter($number, $fractional = true)
    {
        return ($fractional ? $number * 100 : $number) . '%';
    }
}
