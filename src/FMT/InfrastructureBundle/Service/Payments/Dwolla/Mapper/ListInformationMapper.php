<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\ListInformation;

/**
 * Class ListInformationMapper
 */
class ListInformationMapper extends AbstractMapper
{
    /**
     * @param array $source
     *
     * @return ListInformation
     */
    public function map(array $source): ListInformation
    {
        $result = new ListInformation($source['total'] ?? count($source['_embedded']));
        $result
            ->setSelfLink($source['_links']['self']['href'])
            ->setFirstLink($source['_links']['first']['href'] ?? null)
            ->setLastLink($source['_links']['last']['href'] ?? null)
            ->setNextLink($source['_links']['next']['href'] ?? null)
        ;

        return $result;
    }
}
