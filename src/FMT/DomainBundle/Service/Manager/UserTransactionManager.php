<?php

namespace FMT\DomainBundle\Service\Manager;

use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Model\BaseFilterOptions;
use FMT\DomainBundle\Repository\UserTransactionRepositoryInterface;
use FMT\DomainBundle\Service\UserTransactionManagerInterface;
use FOS\UserBundle\Model\UserInterface;

/**
 * Class UserTransactionManager
 * @package FMT\DomainBundle\Service\Manager
 */
class UserTransactionManager extends EventBasedManager implements UserTransactionManagerInterface
{

    /** @var UserTransactionRepositoryInterface */
    private $repository;

    /**
     * UserTransactionManager constructor.
     * @param UserTransactionRepositoryInterface $repository
     */
    public function __construct(UserTransactionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param BaseFilterOptions $filter
     * @param User $donor
     * @return QueryBuilder
     */
    public function getDonorTransactionByFilterQB(BaseFilterOptions $filter, User $donor)
    {
        return $this->repository->getDonorTransactionByFilterQB($filter, $donor);
    }

    /**
     * @param BaseFilterOptions $filter
     * @param User $student
     * @return QueryBuilder
     */
    public function getStudentTransactionByFilterQB(BaseFilterOptions $filter, User $student)
    {
        return $this->repository->getStudentTransactionByFilterQB($filter, $student);
    }

    /**
     * @param BaseFilterOptions $filter
     * @param UserInterface $donor
     * @return array
     */
    public function getDonorTransactionIdsByFilter(BaseFilterOptions $filter, UserInterface $donor)
    {
        return $this->repository->getDonorTransactionIdsByFilter($filter, $donor);
    }

    /**
     * @param BaseFilterOptions $filter
     * @param UserInterface $student
     * @return array
     */
    public function getStudentTransactionIdsByFilter(BaseFilterOptions $filter, UserInterface $student)
    {
        return $this->repository->getStudentTransactionIdsByFilter($filter, $student);
    }

    /**
     * @param UserInterface $donor
     * @return array
     */
    public function getThanksData(UserInterface $donor)
    {
        return $this->repository->getThanksData($donor);
    }
}
