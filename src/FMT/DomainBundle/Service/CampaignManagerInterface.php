<?php

namespace FMT\DomainBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\CampaignContact;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserContact;
use FMT\DataBundle\Model\BaseFilterOptions;
use FMT\DomainBundle\Repository\CampaignRepositoryInterface;

/**
 * Interface CampaignManagerInterface
 * @package FMT\DomainBundle\Service
 */
interface CampaignManagerInterface
{
    /**
     * @param User $user
     * @return Campaign
     */
    public function prepareNew(User $user);

    /**
     * @param Campaign $campaign
     * @throws \Exception
     * @return bool
     */
    public function create(Campaign $campaign);

    /**
     * @param Campaign $campaign
     * @throws \Exception
     * @return bool
     */
    public function update(Campaign $campaign);

    /**
     * @param Campaign $campaign
     */
    public function updateTotals(Campaign $campaign);

    /**
     * @return CampaignRepositoryInterface
     * @deprecated DO NOT USE REPOSITORY DIRECTLY!!!
     */
    public function getRepository();

    /**
     * @param Campaign $campaign
     * @param UserContact $contact
     * @return CampaignContact
     */
    public function assignContact(Campaign $campaign, UserContact $contact);

    /**
     * @param BaseFilterOptions $formFilterParams
     * @return QueryBuilder
     */
    public function getByFilter(BaseFilterOptions $formFilterParams);

    /**
     * @param BaseFilterOptions $baseFilterParams
     * @return int
     */
    public function getTotalCountByFilter(BaseFilterOptions $baseFilterParams);

    /**
     * @param Campaign $campaign
     * @param string   $stringAmount
     *
     * @return array
     */
    public function validateDonateAmount(Campaign $campaign, string $stringAmount): array;

    /**
     * @param Campaign $campaign
     * @return void
     */
    public function updateTotalsByTransactions(Campaign $campaign);

    /**
     * @param int $id
     * @return Campaign
     */
    public function findOrCreate(int $id): Campaign;

    /**
     * @param int $count
     * @return Campaign[]|ArrayCollection
     */
    public function getRandomActiveCampaigns(int $count);

    /**
     * @param \DateTime|null $date
     */
    public function handleStartedToday($date = null);

    /**
     * @param \DateTime|null $date
     */
    public function handleFinishedToday($date = null);
}
