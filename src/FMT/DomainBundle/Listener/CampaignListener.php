<?php

namespace FMT\DomainBundle\Listener;

use FMT\DataBundle\Entity\Campaign;
use FMT\DomainBundle\Event\CampaignEvent;
use FMT\InfrastructureBundle\Helper\NotificationHelper;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class CampaignListener
 * @package FMT\PublicBundle\Listener
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class CampaignListener
{
    /**
     * @var EngineInterface
     */
    private $parser;

    /**
     * @param EngineInterface $engine
     * @required
     */
    public function setEngine(EngineInterface $engine)
    {
        $this->parser = $engine;
    }

    /**
     * @param CampaignEvent $event
     */
    public function onCampaignStarted(CampaignEvent $event)
    {
        /** @var Campaign $campaign */
        $campaign = $event->getCampaign();
        $student = $campaign->getUser();
        $contacts = $campaign->getContacts();

        foreach ($contacts as $campaignContact) {
            $donor = $campaignContact->getContact()->getDonor();
            $message = $this->parser->render(
                '@Public/emails/campaign_notification.email.twig',
                compact('student', 'donor', 'campaign')
            );
            NotificationHelper::submitFromTemplate($message, $donor->getProfile()->getEmail());
        }
    }
}
