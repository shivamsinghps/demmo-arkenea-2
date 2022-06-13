<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 13:13
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\AddItemResult;

class AddItemResultMapper extends AbstractMapper
{
    public function map(array $source) : AddItemResult
    {
        $result = new AddItemResult();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->familyId = $sourceWrapper->getString("ProductFamilyId");
        $resultWrapper->sku = $sourceWrapper->getString("Sku");
        $resultWrapper->result = $sourceWrapper->getInt("Result");
        $resultWrapper->message = $sourceWrapper->getString("Message");

        return $result;
    }
}
