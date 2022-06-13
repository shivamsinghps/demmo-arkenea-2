<?php
/**
 * Author: Anton Orlov
 * Date: 23.03.2018
 * Time: 11:01
 */

namespace FMT\DomainBundle\Event;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class UserEvent
 * @package FMT\DomainBundle\Event
 */
class UserEvent extends Event
{
    const SIGNUP_STARTED = "fmt.signup_started";
    const SIGNUP_SUCCESS = "fmt.signup_success";
    const SIGNUP_FAILED = "fmt.signup_failed";
    const LOGIN_STARTED = "fmt.login_started";
    const LOGIN_SUCCESS = "fmt.login_success";
    const LOGIN_FAILED = "fmt.login_failed";
    const CONFIRMATION_RECEIVED = "fmt.confirmation_received";
    const CONFIRMATION_SUCCESS = "fmt.confirmation_success";
    const CONFIRMATION_FAILED = "fmt.confirmation_failed";
    const USER_UPDATED = "fmt.user_updated";
    const USER_CONTACT_ADDED = "fmt.user_contact_added";
    const CONTACT_SIGNUP_INITIATED = 'fmt.contact_signup_initiated';
    const CONTACT_SIGNUP_SUCCESS = 'fmt.contact_signup_success';
    const USER_PROFILE_UPDATED = "fmt.user_profile_updated";

    /** @var UserInterface */
    private $user;

    /**
     * UserEvent constructor.
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user = null)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
