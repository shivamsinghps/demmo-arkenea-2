<?php
/**
 * Author: Anton Orlov
 * Date: 23.03.2018
 * Time: 11:14
 */

namespace FMT\DomainBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserContact;
use FMT\DataBundle\Entity\UserMajor;
use FMT\DataBundle\Entity\UserSchool;
use FMT\DataBundle\Model\BaseFilterOptions;
use FOS\UserBundle\Model\UserInterface;

/**
 * Interface UserManagerInterface
 * @package FMT\DomainBundle\Service
 */
interface UserManagerInterface
{
    /**
     * Returns active student by student ID
     *
     * @param int $id
     * @return User
     */
    public function getActiveStudent($id);

    /**
     * @param User $user
     */
    public function confirm(User $user);

    /**
     * @param User $user
     */
    public function create(User $user);

    /**
     * @param User $user
     * @param bool $allowNotEnabled
     */
    public function update(User $user, bool $allowNotEnabled = false);

    /**
     * @param bool $forActiveCampaign
     * @return UserMajor[]|ArrayCollection
     */
    public function getMajors($forActiveCampaign = false);

    /**
     * @return UserSchool[]|array
     */
    public function getSchools();

    /**
     * @param $email
     * @return null|object|User
     */
    public function getUserByEmail($email);

    /**
     * @param User $user
     * @return User
     */
    public function createOrUpdateUser(User $user);

    /**
     * @param User $student
     * @param User $contact
     * @param bool $assignToCampaign
     * @return UserContact
     */
    public function addContact(User $student, User $contact, $assignToCampaign = false);

    /**
     * @return User
     */
    public function makeDonor();

    /**
     * @return User
     */
    public function makeStudent();

    /**
     * @param User $user
     * @return User
     */
    public function completeUser(User $user);

    /**
     * @param BaseFilterOptions $formFilterParams
     * @return QueryBuilder
     */
    public function getDonatedStudentsFiltered(BaseFilterOptions $formFilterParams);

    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function findOrCreateDonorAsContact(UserInterface $user);

    /**
     * @param UserInterface $user
     */
    public function disableAccount(UserInterface $user);
}
