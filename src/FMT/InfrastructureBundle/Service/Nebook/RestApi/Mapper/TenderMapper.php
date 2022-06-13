<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 15:00
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Tender;

class TenderMapper extends AbstractMapper
{
    public function map(array $source) : Tender
    {
        $result = new Tender();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("Id");
        $resultWrapper->backofficeId = $sourceWrapper->getString("BackofficeId");
        $resultWrapper->name = $sourceWrapper->getString("Name");
        $resultWrapper->description = $sourceWrapper->getString("Description");
        $resultWrapper->isCreditCard = $sourceWrapper->getBool("IsCreditCard");
        $resultWrapper->isDisabled = $sourceWrapper->getBool("IsDisabled");
        $resultWrapper->isPromptRequired = $sourceWrapper->getBool("IsPrompt1Required");
        $resultWrapper->isRentalRequired = $sourceWrapper->getBool("IsRentalRequired");
        $resultWrapper->prompt = $sourceWrapper->getString("Prompt1");
        $resultWrapper->regexPattern = $sourceWrapper->getString("RegexPattern");
        $resultWrapper->sortOrder = $sourceWrapper->getInt("SortOrder");
        $resultWrapper->validateBalance = $sourceWrapper->getBool("ValidateBalance");

        return $result;
    }
}
