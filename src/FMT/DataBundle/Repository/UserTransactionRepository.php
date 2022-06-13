<?php
/**
 * Author: Anton Orlov
 * Date: 27.04.2018
 * Time: 16:48
 */

namespace FMT\DataBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserTransaction;
use FMT\DataBundle\Model\BaseFilterOptions;
use FMT\DomainBundle\Repository\UserTransactionRepositoryInterface;
use FOS\UserBundle\Model\UserInterface;

class UserTransactionRepository extends DoctrineRepository implements UserTransactionRepositoryInterface
{
    /**
     * @param BaseFilterOptions $filter
     * @param User $donor
     * @return QueryBuilder
     */
    public function getDonorTransactionByFilterQB(BaseFilterOptions $filter, User $donor)
    {
        $qb = $this
            ->createQueryBuilder('transaction')
            ->join('transaction.sender', 'sender', 'WITH', 'sender.id = ?1')
            ->join('transaction.recipient', 'recipient')
            ->join('recipient.profile', 'profile')
            ->addSelect('(transaction.fee + transaction.net) as HIDDEN amount')
            ->setParameter(1, $donor);

        $this->addSortCondition($qb, $filter);
        $this->addSearchConditionForDonor($qb, $filter);

        return $qb;
    }

    /**
     * @param BaseFilterOptions $filter
     * @param User $student
     * @return QueryBuilder
     */
    public function getStudentTransactionByFilterQB(BaseFilterOptions $filter, User $student)
    {
        $qb = $this
            ->createQueryBuilder('transaction')
            ->addSelect('(transaction.fee + transaction.net) as HIDDEN amount')
            ->where('transaction.sender = ?1')
            ->setParameter(1, $student);

        $this->addSortCondition($qb, $filter);
        $this->addSearchConditionForStudent($qb, $filter);

        return $qb;
    }

    /**
     * @param Campaign $campaign
     * @return ArrayCollection|UserTransaction[]
     */
    public function getTransactionsOfCampaignWithSender(Campaign $campaign)
    {
        return $this->createQueryBuilder('ut')
            ->leftJoin('ut.sender', 'u')
            ->andWhere('ut.campaign = :campaign')
            ->getQuery()
            ->execute([
                'campaign' => $campaign,
            ]);
    }

    /**
     * @param UserInterface $donor
     * @return ArrayCollection|UserTransaction[]
     */
    public function getThanksData(UserInterface $donor)
    {
        return $this->createQueryBuilder('ut')
            ->select("ut.thanks as thanksMessage, 
            ut.net as fundedAmount,
            CONCAT(school.name, ' ', profile.gradYear) as schoolData,
            CONCAT(profile.firstName, ' ', profile.lastName) as studentData")
            ->join('ut.recipient', 'user')
            ->join('user.profile', 'profile')
            ->join('profile.school', 'school')
            ->where('ut.sender = :sender')
            ->andWhere('ut.thanks IS NOT NULL')
            ->setParameter('sender', $donor)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param UserInterface $donor
     * @param UserInterface $student
     * @return array
     */
    public function getCountAndAmountTransactionsByDonor(UserInterface $donor, UserInterface $student)
    {
        return $this->createQueryBuilder('transaction')
            ->select('
                IDENTITY(transaction.campaign) as campaignId,
                COALESCE(SUM(transaction.net + transaction.fee)) as amountTransaction,
                COUNT(transaction.id) as countTransaction')
            ->groupBy('transaction.campaign')
            ->andWhere('transaction.recipient = :student')
            ->andWhere('transaction.sender = :donor')
            ->andWhere('transaction.anonymous = :anonymous')
            ->setParameters([
                'student' => $student,
                'donor' => $donor,
                'anonymous' => false,
            ])
            ->getQuery()
            ->getResult();
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
            $qb->orderBy('transaction.id', $sortDirection);

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
    private function addSearchConditionForDonor(QueryBuilder $qb, BaseFilterOptions $filter)
    {
        if (!$filter->getSearch()) {
            return;
        }

        $qb
            ->andWhere($qb->expr()->orX(
                $qb->expr()->like('profile.firstName', ':searchLike'),
                $qb->expr()->like('profile.lastName', ':searchLike'),
                $qb->expr()->like('CONCAT(profile.firstName, \' \', profile.lastName)', ':searchLike'),
                $qb->expr()->eq('DATE_FORMAT(transaction.date, \'%m/%d/%Y\')', ':search')
            ))
            ->setParameter('search', $filter->getSearch())
            ->setParameter('searchLike', sprintf('%%%s%%', $filter->getSearch()));
    }

    /**
     * @param QueryBuilder $qb
     * @param BaseFilterOptions $filter
     */
    private function addSearchConditionForStudent(QueryBuilder $qb, BaseFilterOptions $filter)
    {
        if (!$filter->getSearch()) {
            return;
        }

        $qb
            ->andWhere('DATE_FORMAT(transaction.date, :format) = :search')
            ->setParameter('format', '%m/%d/%Y')
            ->setParameter('search', $filter->getSearch());
    }

    /**
     * @inheritDoc
     */
    public function findUnprocessedByType(int $type): array
    {
        $qb = $this->createQueryBuilder('UserTransaction');
        $qb
            ->andWhere('UserTransaction.unprocessedAmount > 0')
            ->andWhere('UserTransaction.type = :type')
            ->addOrderBy('UserTransaction.unprocessedAmount', 'ASC')
            ->setParameter('type', $type);

        return $qb->getQuery()->getResult();
    }
}
