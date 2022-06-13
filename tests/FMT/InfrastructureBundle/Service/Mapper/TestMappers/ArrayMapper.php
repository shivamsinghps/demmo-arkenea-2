<?php

declare(strict_types=1);

namespace Tests\FMT\InfrastructureBundle\Service\Mapper\TestMappers;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;

/**
 * Class ArrayMapper
 */
class ArrayMapper extends AbstractMapper
{
    /**
     * @param array $source
     *
     * @return int
     */
    public function mapToSum(array $source): int
    {
        return array_sum(array_map(function ($value) {
            return is_array($value) ? $this->mapper->map($value, 'integer') : $value;
        }, $source));
    }
}
