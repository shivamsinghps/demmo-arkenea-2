<?php

declare(strict_types=1);

namespace Tests\FMT\InfrastructureBundle\Service\Mapper\TestMappers;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;

/**
 * Class IntegerMapper
 */
class IntegerMapper extends AbstractMapper
{
    /**
     * @param int $source
     *
     * @return string
     */
    public function mapToString(int $source): string
    {
        return (string) $source;
    }
}
