<?php

namespace FMT\DataBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserMajor;
use FMT\DataBundle\Entity\UserTransaction;
use FMT\DataBundle\Model\BaseFilterOptions;
use FMT\DomainBundle\Repository\UserRepositoryInterface;
use FMT\InfrastructureBundle\Helper\DateHelper;

/**
 * Class UserRepository
 * @package FMT\DataBundle\Repository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserRepository extends DoctrineRepository implements UserRepositoryInterface
{
    /**
     * @param $token
     * @return null|object
     */
    public function findUserByConfirmationToken($token)
    {
        return $this->findOneBy(['confirmationToken' => $token]);
    }

    /**
     * @param string $email
     * @return User
     */
    public function findByEmail($email)
    {
        return $this->findOneBy(["login" => $email]);
    }

    /**
     * @param User $user
     * @return User
     */
    public function findOrCreate(User $user)
    {
        $result = $this->findByEmail($user->getLogin());

        if (empty($result)) {
            $result = $this->save($user);
        }

        return $result;
    }

    /**
     * @param bool $forUpdate
     * @param bool $forActiveCampaign
     * @param array $visibilityData
     * @return ArrayCollection|UserMajor[]
     */
    public function getMajors($forUpdate = false, $forActiveCampaign = false, array $visibilityData = [])
    {
        $qb = $this->createQueryBuilderOf(UserMajor::class, 'um');

        if (!$forUpdate) {
            $qb->where('um.active = true');
        }

        if ($forActiveCampaign) {
            $utcNow = DateHelper::getUtcNow();
            $qb
                ->select('um')
                ->join('um.profile', 'profile')
                ->join('profile.user', 'user')
                ->join('user.campaigns', 'campaign')
                ->andWhere('campaign.startDate <= :now')
                ->andWhere('campaign.endDate >= :now')
                ->andWhere('campaign.isPaused = :isPaused')
                ->andWhere($qb->expr()->in('profile.visible', ':visible'))
                ->setParameter('now', $utcNow)
                ->setParameter('isPaused', false)
                ->setParameter('visible', $visibilityData);
        }

        $qb->orderBy("um.name", "ASC");

        if ($forUpdate) {
            $result = $this
                ->getEm()
                ->createQuery($qb->getDQL())
                ->setLockMode(LockMode::PESSIMISTIC_WRITE)
                ->getResult();
        } else {
            $result = $qb->getQuery()->getResult();
        }

        return new ArrayCollection($result);
    }

    /**
     * @return \DateTime
     */
    public function getMajorSyncDate()
    {
        $result = $this
            ->createQueryBuilderOf(UserMajor::class, 'um')
            ->select('MAX(um.updatedAt)')
            ->andWhere('um.active = :isActive')
            ->setParameter('isActive', true)
            ->getQuery()
            ->getSingleScalarResult();

        return $this->convertStringToUtcDateTime($result);
    }

    /**
     * @param BaseFilterOptions $formFilterParams
     * @param User $donor
     * @return QueryBuilder
     */
    public function getDonatedStudentsFiltered(BaseFilterOptions $formFilterParams, User $donor)
    {
        $qb = $this->createQueryBuilder('u')
            ->join(UserTransaction::class, 'transaction', Join::WITH, 'u = transaction.recipient')
            ->join('u.profile', 'profile')
            ->andWhere('transaction.sender = :donor')
            ->setParameter('donor', $donor);

        if ($formFilterParams->getSearch()) {
            $qb->andWhere('CONCAT(profile.firstName, :space , profile.lastName) LIKE :search')
                ->setParameter('space', ' ')
                ->setParameter('search', '%' . $formFilterParams->getSearch() . '%');
        }
        $qb->orderBy('profile.lastName', 'asc');

        return $qb;
    }

    /**
     * @param User $user
     * @return int
     */
    public function getRegistrationsAmountOfUser(User $user)
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u)')
            ->where('u.email = :email')
            ->orWhere('u.email LIKE :emailLike')
            ->setParameters([
                'email' => $user->getEmail(),
                'emailLike' => sprintf('%s%s%%', $user->getEmail(), User::DELETED_USER_DELIMITER_MARK)
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }
}
