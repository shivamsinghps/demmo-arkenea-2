<?php

namespace FMT\DataBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use FMT\DomainBundle\Repository\UserMajorRepositoryInterface;

/**
 * Class UserMajorRepository
 * @package FMT\DataBundle\Repository
 * @deprecated Use corresponding methods of UserRepository
 */
class UserMajorRepository extends DoctrineRepository implements UserMajorRepositoryInterface
{
    /**
     * @return \DateTime|null
     */
    public function getMaxdMajorDate()
    {
        $result = $this
            ->createQueryBuilder('um')
            ->select('MAX(um.updatedAt)')
            ->andWhere('um.active = :isActive')
            ->setParameter('isActive', true)
            ->getQuery()
            ->getSingleScalarResult();

        return $this->convertStringToUtcDateTime($result);
    }

    /**
     * @param bool $active
     * @param bool $lock
     * @return ArrayCollection
     */
    public function getMajorList(bool $active = true, bool $lock = false)
    {
        $qb = $this->createQueryBuilder('um');

        if ($active) {
            $qb->where('um.active = true');
        }

        $query = $this
            ->getEm()
            ->createQuery($qb->getDQL());

        if ($lock) {
            $query->setLockMode(LockMode::PESSIMISTIC_WRITE);
        }

        return new ArrayCollection($query->getResult());
    }
}
