<?php

namespace FMT\PublicBundle\Twig;

use FMT\InfrastructureBundle\Helper\DateHelper;
use Twig_Extension;
use \Twig_SimpleFilter;

/**
 * Class DateExtension
 * @package FMT\PublicBundle\Twig
 */
class DateExtension extends Twig_Extension
{
    const NAME = 'date';

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('textual_month_format', [$this, 'textualMonthFormatFilter'], [
                'needs_environment' => true,
            ]),
            new Twig_SimpleFilter('standard_date_format', [$this, 'standardDateFormatFilter'], [
                'needs_environment' => true,
            ]),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param \Twig_Environment $env
     * @param \DateTime|null $date
     * @return string
     */
    public function textualMonthFormatFilter(\Twig_Environment $env, \DateTime $date = null)
    {
        if (!$date) {
            return '';
        }

        $newDate = clone $date;
        $timezone = $env->getExtension('Twig_Extension_Core')->getTimezone();

        return $newDate->setTimezone($timezone)->format(DateHelper::PHP_TEXTUAL_MONTH_FORMAT);
    }

    /**
     * @param \Twig_Environment $env
     * @param \DateTime|null $date
     * @return string
     */
    public function standardDateFormatFilter(\Twig_Environment $env, \DateTime $date = null)
    {
        if (!$date) {
            return '';
        }

        $newDate = clone $date;
        $timezone = $env->getExtension('Twig_Extension_Core')->getTimezone();

        return $newDate->setTimezone($timezone)->format(DateHelper::PHP_STANDARD_FORMAT);
    }
}
