<?php

namespace FMT\DomainBundle\Service;

use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserContact;

/**
 * Interface ContactManagerInterface
 * @package FMT\DomainBundle\Service
 */
interface UserContactManagerInterface
{
    /**
     * @param UserContact $contact
     * @param User $student
     * @param $personalNote
     * @return mixed
     */
    public function inviteContactToCurrentCampaign(UserContact $contact, User $student, $personalNote);

    /**
     * @param UserContact $contact
     * @return mixed
     */
    public function removeContact(UserContact $contact);
}
