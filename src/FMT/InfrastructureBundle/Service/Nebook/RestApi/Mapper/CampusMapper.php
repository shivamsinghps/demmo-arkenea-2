<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 15:07
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Campus;

class CampusMapper extends AbstractMapper
{
    public function map(array $data) : Campus
    {
        $result = new Campus();
        $sourceWrapper = new DataHelper($data);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("Id");
        $resultWrapper->backofficeId = $sourceWrapper->getString("BackofficeId");
        $resultWrapper->name = $sourceWrapper->getString("Name");

        return $result;
    }
}
