<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 17:31
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Requirement;

class RequirementMapper extends AbstractMapper
{
    public function map(array $source) : Requirement
    {
        $result = new Requirement();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("Id");
        $resultWrapper->backofficeId = $sourceWrapper->getString("BackofficeId");
        $resultWrapper->name = $sourceWrapper->getString("Name");
        $resultWrapper->description = $sourceWrapper->getString("Description");
        $resultWrapper->sortOrder = $sourceWrapper->getInt("SortOrder");

        return $result;
    }
}
