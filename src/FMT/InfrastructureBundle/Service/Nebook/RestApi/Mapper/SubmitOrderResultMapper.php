<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 17:31
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Order;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\SubmitOrderResult;

class SubmitOrderResultMapper extends AbstractMapper
{
    public function map(array $source) : SubmitOrderResult
    {
        $result = new SubmitOrderResult();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->response = $sourceWrapper->getInt("Response");
        $resultWrapper->message = $sourceWrapper->getString("Message");
        $resultWrapper->order = $this->mapper->map($sourceWrapper->get("Order"), Order::class);

        return $result;
    }
}
