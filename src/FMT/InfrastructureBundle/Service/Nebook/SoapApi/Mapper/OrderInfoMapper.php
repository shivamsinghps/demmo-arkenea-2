<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\OrderInfo;

/**
 * OrderInfoMapper
 */
class OrderInfoMapper extends AbstractMapper
{    
    /**
     * @param  array $source
     * @return OrderInfo
     */
    public function map(array $source) : OrderInfo
    {
        $result = new OrderInfo();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("OrderID");
        $resultWrapper->status = $sourceWrapper->getString("OrderStatus");

        return $result;
    }
}
