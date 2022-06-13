<?php
/**
 * Author: Anton Orlov
 * Date: 03.03.2018
 * Time: 18:49
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\OperationResult;

class OperationResultMapper extends AbstractMapper
{
    public function map(array $source) : OperationResult
    {
        $result = new OperationResult();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->success = $sourceWrapper->getBool("IsSuccess");
        $resultWrapper->message = $sourceWrapper->getString("Message");

        $advanced = [];
        foreach ($source as $key => $value) {
            if ($key !== "IsSuccess" && $key != "Message") {
                $advanced[$key] = $value;
            }
        }

        if (!empty($advanced)) {
            $resultWrapper->advanced = $advanced;
        }

        return $result;
    }
}
