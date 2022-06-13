<?php

namespace FMT\DomainBundle\Service\Manager;

use FMT\DataBundle\Mapper\ShippingOptionMapper;
use FMT\DomainBundle\Service\ShippingManagerInterface;
use FMT\DomainBundle\Type\Cache\Settings;
use FMT\InfrastructureBundle\Helper\CacheHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Client;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ShippingInfo as NebookShippingInfo;

/**
 * Class ShippingManager
 * @package FMT\DomainBundle\Service\Manager
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShippingManager extends EventBasedManager implements ShippingManagerInterface
{
    /** @var Client */
    private $client;

    /** @var int */
    private $cacheTimeout;

    public function __construct(Client $client, Settings $cacheSettings)
    {
        $this->client = $client;
        $this->cacheTimeout = $cacheSettings->nebookCatalogTimeout;
    }

    /**
     * @return NebookShippingInfo[]
     */
    public function getOptions()
    {
        $callable = function () {
            return $this->client->shippingCodesGetAll();
        };

        $list = CacheHelper::cache('shipping_codes', $callable, $this->cacheTimeout);
        foreach ($list as &$item) {
            $item = ShippingOptionMapper::map($item);
        }

        return $list;
    }
}
