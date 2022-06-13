<?php

namespace FMT\PublicBundle\Voter;

use FMT\DataBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class UserVoter
 * @package FMT\PublicBundle\Voter
 */
class UserVoter extends Voter
{
    const CAN_ADD_CAMPAIGN = 'canAddCampaign';
    const CAN_SHARE_FB = 'canShareUserFB';
    const CAN_SHARE_TW = 'canShareUserTW';
    const CAN_SEE_PRIVATE_ELEMENTS = 'canSeePrivateElements';
    const CAN_DELETE_ACCOUNT = 'canDeleteAccount';

    const AVAILABLE_METHODS = [
        self::CAN_ADD_CAMPAIGN,
        self::CAN_SHARE_FB,
        self::CAN_SHARE_TW,
        self::CAN_SEE_PRIVATE_ELEMENTS,
        self::CAN_DELETE_ACCOUNT,
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
        /** @var User $user */
        $user = $token->getUser();
        if (!$subject instanceof User) {
            return false;
        }

        if (method_exists($this, $attribute)) {
            return $this->$attribute($subject, $user);
        }

        return false;
    }

    /**
     * @param User $owner
     * @param User|string $currentUser
     * @return bool
     */
    protected function canAddCampaign(User $owner, $currentUser)
    {
        return $owner === $currentUser && !$owner->hasUnfinishedCampaign();
    }

    /**
     * @param User $owner
     * @return bool
     */
    protected function canShareUserFB(User $owner)
    {
        $profile = $owner->getProfile();

        return $profile->isVisibleForAll() && $profile->isFacebook();
    }

    /**
     * @param User $owner
     * @return bool
     */
    protected function canShareUserTW(User $owner)
    {
        $profile = $owner->getProfile();

        return $profile->isVisibleForAll() && $profile->isTwitter();
    }

    /**
     * @param User $owner
     * @param User|string $currentUser
     * @return bool
     */
    protected function canSeePrivateElements(User $owner, $currentUser)
    {
        return $owner === $currentUser;
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    protected function canDeleteAccount(UserInterface $user)
    {
        $campaign = $user->getUnfinishedCampaign();
        if (null === $campaign) {
            return true;
        }

        return !$campaign->isPositiveBalance();
    }
}
