<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\TaxMethod;

/**
 * TaxMapper
 */
class TaxMapper extends AbstractMapper
{    
    /**
     * @param  array $source
     * @return TaxMethod
     */
    public function map(array $source) : TaxMethod
    {
        $result = new TaxMethod();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("TaxMethodID");
        $resultWrapper->state = $sourceWrapper->getString("TaxState");
        $resultWrapper->amount = $this->toIntPrice($sourceWrapper->getString("TaxAmount"));
        $resultWrapper->shipInd = $sourceWrapper->getBool("TaxShipInd");

        return $result;
    }
}
