<?php

namespace FMT\DomainBundle\Repository;

use FMT\DataBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;

/**
 * Interface UserStatisticRepositoryInterface
 * @package FMT\DomainBundle\Repository
 */
interface UserStatisticRepositoryInterface
{
    /**
     * @param User $user
     */
    public function updateStudentsFounded(User $user);

    /**
     * @param User $user
     */
    public function updateBooksPurchasedFor(User $user);

    /**
     * @param User $user
     */
    public function updateAmountFounded(User $user);

    /**
     * @param UserInterface $user
     */
    public function updateAmountDonatedTo(UserInterface $user);
}
