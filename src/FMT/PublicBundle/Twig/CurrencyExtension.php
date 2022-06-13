<?php

namespace FMT\PublicBundle\Twig;

use FMT\InfrastructureBundle\Helper\CurrencyHelper;
use \Twig_Extension;
use \Twig_SimpleFilter;

/**
 * Class CurrencyExtension
 * @package FMT\PublicBundle\Twig
 */
class CurrencyExtension extends Twig_Extension
{
    const NAME = 'currency';

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('price', [$this, 'priceFilter']),
            new Twig_SimpleFilter('number', [$this, 'numberFilter']),
            new Twig_SimpleFilter('percent', [$this, 'percentFilter']),
        ];
    }

    /**
     * @param $cents
     * @param int $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     * @return string
     */
    public function priceFilter($cents = 0, $decimals = null, $decPoint = '.', $thousandsSep = '')
    {
        return CurrencyHelper::priceFilter($cents, $decimals, $decPoint, $thousandsSep);
    }

    /**
     * @param $number
     * @param int $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     * @return string
     */
    public function numberFilter($number, $decimals = null, $decPoint = '.', $thousandsSep = '')
    {
        return CurrencyHelper::numberFilter($number, $decimals, $decPoint, $thousandsSep);
    }

    /**
     * @param $number
     * @param bool $fractional
     * @return string
     */
    public function percentFilter($number, $fractional = true)
    {
        return CurrencyHelper::percentFilter($number, $fractional);
    }
}
