<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 13:42
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\PaymentMethod;

class PaymentMethodMapper extends AbstractMapper
{
    public function map(array $source) : PaymentMethod
    {
        $result = new PaymentMethod();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->tenderId = $sourceWrapper->getInt("Tender.Id");
        $resultWrapper->backofficeId = $sourceWrapper->getString("Tender.BackofficeId");
        $resultWrapper->tenderName = $sourceWrapper->getString("Tender.Name");
        $resultWrapper->accountNumber = $sourceWrapper->getString("AccountNumber");
        $resultWrapper->cardNumber = $sourceWrapper->getString("LastFour");
        $resultWrapper->cardCode = $sourceWrapper->getString("CardCode");
        $resultWrapper->expirationDate = $sourceWrapper->getDate("Expiration");
        $resultWrapper->amount = $this->toIntPrice($sourceWrapper->getString("Amount"));

        return $result;
    }
}
