<?php

namespace FMT\DomainBundle\Service;

use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Model\BaseFilterOptions;
use FOS\UserBundle\Model\UserInterface;

interface UserTransactionManagerInterface
{
    /**
     * @param BaseFilterOptions $filter
     * @param User $donor
     * @return QueryBuilder
     */
    public function getDonorTransactionByFilterQB(BaseFilterOptions $filter, User $donor);

    /**
     * @param BaseFilterOptions $filter
     * @param User $student
     * @return QueryBuilder
     */
    public function getStudentTransactionByFilterQB(BaseFilterOptions $filter, User $student);

    /**
     * @param UserInterface $donor
     * @return array
     */
    public function getThanksData(UserInterface $donor);
}
