<?php

namespace FMT\DataBundle\Doctrine\Extensions\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Class UTCDateTimeType
 * @package DoctrineExtensions\DBAL\Types
 */
class UTCDateTimeType extends AbstractDateTimeType
{
    /**
     * {@inheritdoc}
     */
    protected function getFormat(AbstractPlatform $platform)
    {
        return $platform->getDateTimeFormatString();
    }
}
