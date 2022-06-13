<?php

namespace FMT\DataBundle\Doctrine\Extensions\DBAL\Types;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * Class AbstractDateTimeType
 * @package FMT\DataBundle\Doctrine\Extensions\DBAL\Types
 */
abstract class AbstractDateTimeType extends DateTimeType
{
    const DEFAULT_TIME_ZONE = 'UTC';
    /**
     * @param AbstractPlatform $platform
     * @return string
     */
    abstract protected function getFormat(AbstractPlatform $platform);

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof DateTime) {
            $value->setTimezone(new DateTimeZone(self::DEFAULT_TIME_ZONE));
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return bool|DateTime|mixed
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value || $value instanceof DateTime) {
            return $value;
        }

        $converted = DateTime::createFromFormat(
            $this->getFormat($platform),
            $value,
            new DateTimeZone(self::DEFAULT_TIME_ZONE)
        );

        if (! $converted) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $converted->setTimezone(new DateTimeZone(self::DEFAULT_TIME_ZONE));
    }
}
