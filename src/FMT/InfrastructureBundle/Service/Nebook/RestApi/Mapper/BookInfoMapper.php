<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 17:49
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\BookInfo;

class BookInfoMapper extends AbstractMapper
{
    public function map(array $source) : BookInfo
    {
        $result = new BookInfo();
        $resultWrapper = new DataHelper($result);

        foreach ($source as $nvp) {
            if (isset($nvp["Name"])) {
                $name = strtolower($nvp["Name"]);
                $value = isset($nvp["Value"]) ? (string) $nvp["Value"] : null;
                $resultWrapper->set($name, empty($value) ? null : $value);
            }
        }

        return $result;
    }
}
