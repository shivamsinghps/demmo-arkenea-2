<?php

namespace FMT\DataBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\CampaignBook;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserMajor;
use FMT\DataBundle\Entity\UserProfile;
use FMT\DataBundle\Model\BaseFilterOptions;
use FMT\DomainBundle\Repository\CampaignRepositoryInterface;
use FMT\InfrastructureBundle\Helper\DateHelper;

/**
 * Class CampaignRepository
 * @package FMT\DataBundle\Repository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CampaignRepository extends DoctrineRepository implements CampaignRepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createCommonQueryBuilder()
    {
        return $this
            ->createQueryBuilder('c')
            ->join('c.user', 'user')
            ->join('user.profile', 'profile')
            ->join('profile.major', 'major');
    }

    /**
     * @param BaseFilterOptions $filter
     * @param array $visibility
     * @return QueryBuilder
     */
    public function getCampaignByFilter(BaseFilterOptions $filter, array $visibility)
    {
        $qb = $this->createCommonQueryBuilder();
        $this->addActiveCondition($qb);
        $this->addMajorCondition($qb, $filter);
        $this->addSortCondition($qb, $filter);
        $this->addSearchCondition($qb, $filter);
        $this->addVisibilityCondition($qb, $visibility);

        return $qb;
    }

    /**
     * @param User $student
     * @return Campaign|null
     */
    public function getLastFinishedCampaign(User $student)
    {
        $utcNow = DateHelper::getUtcNow();
        $qb = $this->createQueryBuilder('c')
            ->where('c.user = ?1')
            ->andWhere('c.endDate < ?2')
            ->setParameters([1 => $student, 2 => $utcNow])
            ->orderBy('c.endDate', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param BaseFilterOptions $filter
     * @param array $visibility
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotalByFilter(BaseFilterOptions $filter, array $visibility)
    {
        $qb = $this->createCommonQueryBuilder();
        $qb->select('COUNT(c)');
        $this->addActiveCondition($qb);
        $this->addMajorCondition($qb, $filter);
        $this->addSearchCondition($qb, $filter);
        $this->addVisibilityCondition($qb, $visibility);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int $count
     * @return ArrayCollection|Campaign[]
     */
    public function getRandomVisibleActiveCampaigns(int $count)
    {
        $qb = $this->createCommonQueryBuilder();
        $this->addActiveCondition($qb);
        $this->addVisibilityCondition($qb, [UserProfile::VISIBILITY_ALL]);
        $qb->andWhere('(c.fundedTotal + c.purchasedTotal)<(c.estimatedCost + c.estimatedShipping)');
        $qb->orderBy('RAND()');
        $qb->setMaxResults($count);

        return $qb->getQuery()->execute();
    }

    /**
     * @param \DateTime $date
     * @return Campaign[]|array
     */
    public function getStarted($date)
    {
        $date = $date ?? DateHelper::getUtcNow();

        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.startDate = :date')
            ->andWhere('c.isPaused = :isPaused')
            ->setParameters([
                'date' => $date->format('Y-m-d'),
                'isPaused' => false,
            ]);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \DateTime $date
     * @return Campaign[]|array
     */
    public function getFinished($date)
    {
        $date = $date ?? DateHelper::getUtcNow();

        $qb = $this->createQueryBuilder('c')
            ->join(CampaignBook::class, 'book')
            ->andWhere('c.id = book.campaign')
            ->andWhere('c.endDate = :date')
            ->andWhere('book.status = :status')
            ->andWhere('c.isPaused = :isPaused')
            ->setParameters([
                'date' => $date->sub(new \DateInterval('P1D'))->format('Y-m-d'),
                'isPaused' => false,
                'status' => CampaignBook::STATUS_AVAILABLE,
            ]);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $qb
     * @param BaseFilterOptions $filter
     */
    private function addMajorCondition(QueryBuilder $qb, BaseFilterOptions $filter)
    {
        if (!$filter->getMajor() instanceof UserMajor) {
            return;
        }

        $qb
            ->andWhere('major = :major')
            ->setParameter('major', $filter->getMajor());
    }

    /**
     * @param QueryBuilder $qb
     * @param BaseFilterOptions $filter
     */
    private function addSortCondition(QueryBuilder $qb, BaseFilterOptions $filter)
    {
        $sortDirection = $filter->getSortDirection() ?: 'DESC';
        $sortFields = $filter->getSortBy();

        if (!$sortFields) {
            $qb->orderBy('c.startDate', $sortDirection);

            return;
        }

        foreach ($sortFields as $sortField) {
            $qb->addOrderBy($sortField, $sortDirection);
        }
    }

    /**
     * @param QueryBuilder $qb
     * @param BaseFilterOptions $filter
     */
    private function addSearchCondition(QueryBuilder $qb, BaseFilterOptions $filter)
    {
        if (!$filter->getSearch()) {
            return;
        }

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->like('profile.firstName', ':search'),
            $qb->expr()->like('profile.lastName', ':search'),
            $qb->expr()->like('CONCAT(profile.firstName, \' \', profile.lastName)', ':search'),
            $qb->expr()->like('major.name', ':search')
        ));
        $qb->setParameter('search', sprintf('%%%s%%', $filter->getSearch()));
    }

    /**
     * @param QueryBuilder $qb
     */
    private function addActiveCondition(QueryBuilder $qb)
    {
        $utcNow = DateHelper::getUtcNow();
        $qb
            ->andWhere('c.startDate <= :now')
            ->andWhere('c.endDate >= :now')
            ->andWhere('c.isPaused = :isPaused')
            ->setParameters([
                'now' => $utcNow->format('Y-m-d'),
                'isPaused' => false,
            ]);
    }

    /**
     * @param QueryBuilder $qb
     * @param $visibility
     */
    private function addVisibilityCondition(QueryBuilder $qb, array $visibility)
    {
        $qb
            ->andWhere($qb->expr()->in('profile.visible', ':visible'))
            ->setParameter('visible', $visibility);
    }
}
