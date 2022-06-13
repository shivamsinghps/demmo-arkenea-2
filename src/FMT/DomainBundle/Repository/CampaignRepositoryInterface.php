<?php

namespace FMT\DomainBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Model\BaseFilterOptions;

/**
 * Interface CampaignRepositoryInterface
 * @package FMT\DomainBundle\Repository
 */
interface CampaignRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createCommonQueryBuilder();

    /**
     * @param BaseFilterOptions $filter
     * @param array $visibility
     * @return QueryBuilder
     */
    public function getCampaignByFilter(BaseFilterOptions $filter, array $visibility);

    /**
     * @param User $student
     * @return Campaign|null
     */
    public function getLastFinishedCampaign(User $student);

    /**
     * @param BaseFilterOptions $filter
     * @param array $visibility
     * @return integer
     */
    public function getTotalByFilter(BaseFilterOptions $filter, array $visibility);

    /**
     * @param int $count
     * @return ArrayCollection|Campaign[]
     */
    public function getRandomVisibleActiveCampaigns(int $count);

    /**
     * @param \DateTime $date
     * @return Campaign[]|array
     */
    public function getStarted($date);
}
