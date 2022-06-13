<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 17:03
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\OrderComment;

class OrderCommentMapper extends AbstractMapper
{
    public function map(array $source) : OrderComment
    {
        $result = new OrderComment();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->date = $sourceWrapper->getDate("DateEntered");
        $resultWrapper->commentor = $sourceWrapper->getString("EnteredBy");
        $resultWrapper->message = $sourceWrapper->getString("Comment");
        $resultWrapper->internal = $sourceWrapper->getBool("IsInternalOnly");

        return $result;
    }
}
