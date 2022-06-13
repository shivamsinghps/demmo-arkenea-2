<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\Mapper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\TaxShipping;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\ShippingCode;

/**
 * TaxShippingMapper
 */
class TaxShippingMapper extends AbstractMapper
{    
    /**
     * @param  array $source
     * @return TaxShipping
     */
    public function map(array $source): TaxShipping
    {
        $result = new TaxShipping();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $shippingCodes = Mapper::mapList($source['ShippingCodes']['ShippingCode'], ShippingCode::class);

        $resultWrapper->taxAmount = $this->toIntPrice($sourceWrapper->getString("TaxAmount"));
        $resultWrapper->textbookTaxAmount = $this->toIntPrice($sourceWrapper->getString("TextbookTaxAmount"));
        $resultWrapper->taxShipInd = $sourceWrapper->getFloat("TaxShipInd");
        $resultWrapper->shippingCodes = $shippingCodes;

        return $result;
    }
}
