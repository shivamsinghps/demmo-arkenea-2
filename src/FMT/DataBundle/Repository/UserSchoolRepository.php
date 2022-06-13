<?php

namespace FMT\DataBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use FMT\DataBundle\Entity\UserSchool;
use FMT\DomainBundle\Repository\UserSchoolRepositoryInterface;

/**
 * Class UserSchoolRepository
 * @package FMT\DataBundle\Repository
 */
class UserSchoolRepository extends DoctrineRepository implements UserSchoolRepositoryInterface
{
    /**
     * @param array $criteria
     * @return array|UserSchool[]
     * @deprecated
     */
    public function findAllBy(array $criteria)
    {
        return $this->findBy($criteria);
    }

    /**
     * @param bool $activeOnly
     * @return ArrayCollection
     */
    public function getSchoolsCollection($activeOnly = false): ArrayCollection
    {
        $result = $this->getSchoolsQuery($activeOnly)->execute();

        return is_array($result) ? new ArrayCollection($result) : $result;
    }

    /**
     * @param bool $activeOnly
     * @return Query
     */
    private function getSchoolsQuery($activeOnly = false)
    {
        $qb = $this->createQueryBuilder("us");

        if ($activeOnly) {
            $qb->where("us.active = true");
        }

        return $qb->getQuery();
    }

    /**
     * @param bool $activeOnly
     * @return array|ArrayCollection|int|string
     */
    public function getSchools($activeOnly = false)
    {
        return $this->getSchoolsQuery($activeOnly)->getResult();
    }
}
