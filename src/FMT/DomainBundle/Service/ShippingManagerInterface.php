<?php

namespace FMT\DomainBundle\Service;

use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ShippingInfo as NebookShippingInfo;

interface ShippingManagerInterface
{
    /**
     * @return NebookShippingInfo[]
     */
    public function getOptions();
}
