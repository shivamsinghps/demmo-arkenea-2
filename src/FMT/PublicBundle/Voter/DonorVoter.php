<?php

namespace FMT\PublicBundle\Voter;

use FMT\DataBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class DonorVoter
 * @package FMT\PublicBundle\Voter
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DonorVoter extends Voter
{
    const CAN_SHARE_FB = 'canShareDonorFB';
    const CAN_SHARE_TW = 'canShareDonorTW';

    public static $permissionArray = [
        self::CAN_SHARE_FB,
        self::CAN_SHARE_TW,
    ];

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, self::$permissionArray)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (!$subject instanceof User || !$subject->isDonor()) {
            return false;
        }

        if (method_exists($this, $attribute)) {
            return $this->$attribute($subject);
        }

        return false;
    }

    /**
     * @param User $owner
     * @return bool
     */
    protected function canShareDonorFB(User $owner)
    {
        $profile = $owner->getProfile();

        return $profile->isVisibleForAll() && $profile->isFacebook();
    }

    /**
     * @param User $owner
     * @return bool
     */
    protected function canShareDonorTW(User $owner)
    {
        $profile = $owner->getProfile();

        return $profile->isVisibleForAll() && $profile->isTwitter();
    }
}
