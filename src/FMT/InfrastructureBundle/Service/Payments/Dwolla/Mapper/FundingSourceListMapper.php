<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\FundingSource;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\FundingSourceList;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\ListInformation;

/**
 * Class FundingSourceListMapper
 */
class FundingSourceListMapper extends AbstractMapper
{
    /**
     * @param array $source
     *
     * @return FundingSourceList
     */
    public function map(array $source): FundingSourceList
    {
        $fundingSources = $this->mapper->mapList($source['_embedded']['funding-sources'], FundingSource::class);

        return new FundingSourceList($fundingSources, new ListInformation(count($source['_embedded'])));
    }
}
