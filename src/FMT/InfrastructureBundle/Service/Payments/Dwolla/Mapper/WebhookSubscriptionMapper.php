<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use DateTime;
use Exception;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\WebhookSubscription;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;

/**
 * Class WebhookSubscriptionMapper
 */
class WebhookSubscriptionMapper extends AbstractMapper
{
    /**
     * @param WebhookSubscription $source
     *
     * @return array
     */
    public function mapToArray(WebhookSubscription $source): array
    {
        return [
            'url' => $source->getUrl(),
            'secret' => $source->getSecret(),
        ];
    }

    /**
     * @param array $source
     *
     * @return WebhookSubscription
     * @throws Exception
     */
    public function mapFromArray(array $source): WebhookSubscription
    {
        $result = new WebhookSubscription();
        $result
            ->setId($source['id'])
            ->setIri($source['_links']['self']['href'])
            ->setUrl($source['url'])
            ->setPaused($source['paused'])
            ->setCreated(new DateTime($source['created']))
        ;

        return $result;
    }
}
