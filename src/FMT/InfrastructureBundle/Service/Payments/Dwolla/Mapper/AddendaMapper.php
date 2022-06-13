<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Addenda;

/**
 * Class AddendaMapper
 */
class AddendaMapper extends AbstractMapper
{
    /**
     * @param Addenda $source
     *
     * @return array
     */
    public function mapToArray(Addenda $source): array
    {
        return [
            'values' => $source->getValues(),
        ];
    }

    /**
     * @param array $source
     *
     * @return Addenda
     */
    public function mapFromArray(array $source): Addenda
    {
        return new Addenda($source['values']);
    }
}
