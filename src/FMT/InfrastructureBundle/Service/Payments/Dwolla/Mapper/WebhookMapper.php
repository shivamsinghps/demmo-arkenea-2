<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use DateTime;
use Exception;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Webhook;

/**
 * Class WebhookMapper
 */
class WebhookMapper extends AbstractMapper
{
    /**
     * @param array $source
     *
     * @return Webhook
     * @throws Exception
     */
    public function map(array $source): Webhook
    {
        $result = new Webhook();
        $result
            ->setCreated(new DateTime($source['created']))
            ->setId($source['id'])
            ->setResourceId($source['resourceId'])
            ->setTopic($source['topic'])
            ->setSelf($source['_links']['self']['href'] ?? null)
            ->setAccount($source['_links']['account']['href'] ?? null)
            ->setResource($source['_links']['resource']['href'] ?? null)
            ->setCustomer($source['_links']['customer']['href'] ?? null)
        ;

        return $result;
    }
}
