<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 15:11
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Campus;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Term;

class TermMapper extends AbstractMapper
{
    public function map(array $source) : Term
    {
        $result = new Term();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("Id");
        $resultWrapper->backofficeId = $sourceWrapper->getInt("BackofficeId");
        $resultWrapper->name = $sourceWrapper->getString("Name");
        $resultWrapper->description = $sourceWrapper->getString("Description");
        $resultWrapper->campus = $this->mapper->map($sourceWrapper->get("Campus"), Campus::class);
        $resultWrapper->preorderCreateStartDate = $sourceWrapper->getDate("PreorderCreateStartDate");
        $resultWrapper->preorderCreateEndDate = $sourceWrapper->getDate("PreorderCreateEndDate");
        $resultWrapper->preorderGenerateDate = $sourceWrapper->getDate("PreorderGenerateDate");
        $resultWrapper->preorderStopEditDate = $sourceWrapper->getDate("PreorderStopEditDate");
        $resultWrapper->reservationEndDate = $sourceWrapper->getDate("ReservationEndDate");
        $resultWrapper->reservationPickupEndDate = $sourceWrapper->getDate("ReservationPickupEndDate");
        $resultWrapper->sortOrder = $sourceWrapper->getInt("SortOrder");
        $resultWrapper->adoptionStatus = $sourceWrapper->getInt("AdoptionStatus");
        $resultWrapper->status = $sourceWrapper->getInt("Status");

        return $result;
    }
}
