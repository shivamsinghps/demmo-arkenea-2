<?php

namespace FMT\DomainBundle\Event;

use FMT\DataBundle\Entity\Campaign;
use Symfony\Component\EventDispatcher\Event;

class CampaignEvent extends Event
{
    const CAMPAIGN_CREATED = "fmt.campaign_created";
    const CAMPAIGN_STARTED = "fmt.campaign_started";
    const CAMPAIGN_UPDATED = "fmt.campaign_updated";
    const CAMPAIGN_FINISHED = "fmt.campaign_finished";
    const CAMPAIGN_FAILED = "fmt.campaign_failed";
    const CAMPAIGN_PAUSED = "fmt.campaign_paused";
    const CAMPAIGN_RESTARTED = "fmt.campaign_restarted";
    const CAMPAIGN_CONTACT_ADDED = "fmt.campaign_contact_added";

    /** @var Campaign */
    private $campaign;

    /**
     * CampaignEvent constructor.
     * @param Campaign $campaign
     */
    public function __construct(Campaign $campaign = null)
    {
        $this->campaign = $campaign;
    }

    /**
     * @return Campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }
}
