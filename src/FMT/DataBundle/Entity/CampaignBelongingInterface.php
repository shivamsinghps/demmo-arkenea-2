<?php

namespace FMT\DataBundle\Entity;

/**
 * Interface CampaignBelongingInterface
 * @package FMT\DataBundle\Entity
 */
interface CampaignBelongingInterface
{
    /**
     * @return Campaign
     */
    public function getCampaign(): Campaign;
}
