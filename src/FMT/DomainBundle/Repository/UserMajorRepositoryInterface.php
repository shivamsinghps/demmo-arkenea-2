<?php

namespace FMT\DomainBundle\Repository;

/**
 * Interface UserMajorRepositoryInterface
 * @package FMT\DomainBundle\Repository
 * @deprecated Use corresponding methods of UserRepositoryInterface
 */

interface UserMajorRepositoryInterface extends RepositoryInterface
{
    /**
     * @return \DateTime|null
     */
    public function getMaxdMajorDate();

    /**
     * @param bool $active
     * @param bool $lock
     * @return mixed
     */
    public function getMajorList(bool $active = true, bool $lock = false);
}
