<?php

namespace FMT\PublicBundle\Voter;

use FMT\DataBundle\Entity\UserContact;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class UserContactVoter
 * @package FMT\PublicBundle\Voter
 */
class UserContactVoter extends Voter
{
    const CAN_DELETE = 'canDeleteContact';

    const AVAILABLE_METHODS = [
        self::CAN_DELETE,
    ];

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, self::AVAILABLE_METHODS)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var UserInterface $user */
        $user = $token->getUser();

        if (!$subject instanceof UserContact || !$user instanceof UserInterface) {
            return false;
        }

        if (method_exists($this, $attribute)) {
            return $this->$attribute($subject, $user);
        }

        return false;
    }

    /**
     * @param UserContact $contact
     * @param UserInterface $user
     * @return bool
     */
    public function canDeleteContact(UserContact $contact, UserInterface $user)
    {
        return $contact->getStudent() === $user;
    }
}
