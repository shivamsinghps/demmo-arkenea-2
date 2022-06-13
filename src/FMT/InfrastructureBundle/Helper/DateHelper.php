<?php
/**
 * Author: Anton Orlov
 * Date: 03.03.2018
 * Time: 15:27
 */

namespace FMT\InfrastructureBundle\Helper;

/**
 * Class DateHelper
 * @package FMT\InfrastructureBundle\Helper
 */
class DateHelper
{
    const PHP_TEXTUAL_MONTH_FORMAT = 'F j, Y';
    const PHP_STANDARD_FORMAT = 'm/d/Y';
    const INTL_FORMAT = 'MM/dd/yyyy';
    const JS_STANDARD_FORMAT = 'mm/dd/yyyy';

    /**
     * Method tries to guess input date format and return DateTime object ot FALSE in case of failure
     *
     * @param mixed $value
     * @return \DateTime|bool
     */
    public static function guess($value)
    {
        $options = array_keys(array_filter([
            "Timestamp" => is_int($value) || preg_match("/^\\d+$/si", $value),
            "DotNet" => substr($value, 0, 6) === "/Date(" && substr($value, -2) == ")/",
            "Standard" => true
        ]));

        if (empty($options)) {
            return false;
        }

        $method = "parseFrom" . array_shift($options);

        return call_user_func([__CLASS__, $method], $value);
    }

    /**
     * Method parses string as UNIX timestamp and returns DateTime object or FALSE in the case of unsupported data
     *
     * @param int|string $timestamp
     * @return \DateTime|bool
     */
    public static function parseFromTimestamp($timestamp)
    {
        return \DateTime::createFromFormat("U", $timestamp);
    }

    /**
     * Method parses string as .NET JSON serialized date object and returns DateTime object or FALSE in the case of
     * incorrect JSON date format
     *
     * @param string $value
     * @return \DateTime|bool
     */
    public static function parseFromDotNet($value)
    {
        $components = null;
        preg_match("/\\/Date\\((\\d+)([+-]\\d{4})?\\)\\//si", $value, $components);

        if (empty($components)) {
            return false;
        }

        $result = \DateTime::createFromFormat("U", substr($components[1], 0, -3));
        if ($result && count($components) === 3) {
            $result->setTimezone(new \DateTimeZone($components[2]));
        }

        return $result;
    }

    /**
     * Method tries to parse string from well-known date formats and returns DateTime object of FALSE in the case of
     * failure
     *
     * @param string $value
     * @return \DateTime|bool
     */
    public static function parseFromStandard($value)
    {
        static $timezone = null;
        static $formats = [
            "Y-m-d H:i:s",
            "Y-m-d\\TH:i:sP",
            "Y-m-d\\TH:i:s.*P",
            "Y-m-d\\TH:i:s\\Z",
            "Y-m-d\\TH:i:s.*\\Z",
            "D, J M Y H:i:s O"
        ];

        if (empty($timezone)) {
            $timezone = new \DateTimeZone("UTC");
        }

        $options = array_filter(array_map(function ($format) use ($value, $timezone) {
            return \DateTime::createFromFormat($format, $value, $timezone);
        }, $formats));

        if (empty($options)) {
            return false;
        }

        return array_shift($options);
    }

    /**
     * @return \DateTime
     */
    public static function getUtcNow()
    {
        $timeZone = new \DateTimeZone("UTC");

        return (new \DateTime())->setTimezone($timeZone);
    }

    /**
     * @return \DateTime
     */
    public static function getUtcToday()
    {
        $timeZone = new \DateTimeZone("UTC");

        return (new \DateTime())->setTimezone($timeZone)->setTime(0, 0);
    }
}
