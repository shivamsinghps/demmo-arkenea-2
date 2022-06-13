<?php
/**
 * Author: Anton Orlov
 * Date: 27.04.2018
 * Time: 16:49
 */

namespace FMT\DomainBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserTransaction;
use FMT\DataBundle\Model\BaseFilterOptions;
use FOS\UserBundle\Model\UserInterface;

interface UserTransactionRepositoryInterface extends RepositoryInterface
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
     * @param Campaign $campaign
     * @return ArrayCollection|UserTransaction[]
     */
    public function getTransactionsOfCampaignWithSender(Campaign $campaign);

    /**
     * @param UserInterface $donor
     * @return ArrayCollection|UserTransaction[]
     */
    public function getThanksData(UserInterface $donor);

    /**
     * @param UserInterface $donor
     * @param UserInterface $student
     * @return array
     */
    public function getCountAndAmountTransactionsByDonor(UserInterface $donor, UserInterface $student);

    /**
     * @param int $type
     *
     * @return UserTransaction[]
     */
    public function findUnprocessedByType(int $type): array;
}
