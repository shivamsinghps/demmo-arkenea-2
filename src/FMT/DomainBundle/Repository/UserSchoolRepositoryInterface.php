<?php

namespace FMT\DomainBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use FMT\DataBundle\Entity\UserSchool;

/**
 * Interface UserSchoolRepositoryInterface
 * @package FMT\DomainBundle\Repository
 */
interface UserSchoolRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $criteria
     * @return array|UserSchool[]
     * @deprecated This method moved into user repository
     */
    public function findAllBy(array $criteria);

    /**
     * @param bool $activeOnly
     * @return array|ArrayCollection|int|string
     */
    public function getSchools($activeOnly = false);

    /**
     * @param bool $activeOnly
     * @return ArrayCollection
     */
    public function getSchoolsCollection($activeOnly = false): ArrayCollection;
}
