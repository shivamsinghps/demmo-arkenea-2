<?php
/**
 * Author: Anton Orlov
 * Date: 10.05.2018
 * Time: 11:45
 */

namespace FMT\DomainBundle\Service\Synchronizer;

use Doctrine\Common\Collections\ArrayCollection;
use FMT\DataBundle\Entity\UserSchool;
use FMT\DomainBundle\Repository\UserSchoolRepositoryInterface;
use FMT\DomainBundle\Service\SynchronizerInterface;

class SchoolSynchronizer implements SynchronizerInterface
{
    const SCHOOL_CACHE_KEY = "fmt_school_cache";

    /** @var UserSchoolRepositoryInterface */
    private $repository;

    /** @var int */
    private $lifetime;

    public function __construct(UserSchoolRepositoryInterface $repository, $lifetime = 3600)
    {
        $this->repository = $repository;
        $this->lifetime = $lifetime;
    }

    /**
     * @return UserSchool[]|ArrayCollection|array
     */
    public function synchronize()
    {
        return $this->repository->getSchools(true);
    }
}
