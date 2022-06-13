<?php

namespace FMT\DomainBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserMajor;
use FMT\DataBundle\Model\BaseFilterOptions;

/**
 * Interface UserRepositoryInterface
 * @package FMT\DomainBundle\Repository
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $token
     * @return null|object
     */
    public function findUserByConfirmationToken($token);

    /**
     * @param string $email
     * @return User
     */
    public function findByEmail($email);

    /**
     * @param User $user
     * @return User
     */
    public function findOrCreate(User $user);

    /**
     * @param bool $forUpdate
     * @param bool $forActiveCampaign
     * @param array $visibilityData
     * @return UserMajor[]|ArrayCollection
     */
    public function getMajors($forUpdate = false, $forActiveCampaign = false, array $visibilityData = []);

    /**
     * @return \DateTime
     */
    public function getMajorSyncDate();

    /**
     * @param BaseFilterOptions $formFilterParams
     * @param User $donor
     * @return QueryBuilder
     */
    public function getDonatedStudentsFiltered(BaseFilterOptions $formFilterParams, User $donor);

    /**
     * @param User $user
     * @return int
     */
    public function getRegistrationsAmountOfUser(User $user);
}
