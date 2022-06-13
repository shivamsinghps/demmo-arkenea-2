<?php

namespace FMT\PublicBundle\Voter;

use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserProfile;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class CampaignVoter
 * @package FMT\PublicBundle\Controller\Voter
 */
class CampaignVoter extends Voter
{
    const CAN_EDIT = 'canEdit';
    const CAN_VIEW = 'canView';
    const CAN_SEE_DAYS_LEFT = 'canSeeDaysLeft';
    const CAN_FUND = 'canFund';
    const CAN_SHARE_FB = 'canShareFB';
    const CAN_SHARE_TW = 'canShareTW';
    const CAN_SEE_SUMMARY = 'canSeeSummary';

    protected static $allPermissions = [
        self::CAN_EDIT,
        self::CAN_VIEW,
        self::CAN_SEE_DAYS_LEFT,
        self::CAN_FUND,
        self::CAN_SHARE_FB,
        self::CAN_SHARE_TW,
        self::CAN_SEE_SUMMARY,
    ];

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, self::$allPermissions)) {
            return false;
        }

        if (!$subject instanceof Campaign) {
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
        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof User && $user != 'anon.') {
            return false;
        }

        if (method_exists($this, $attribute)) {
            return $this->$attribute($subject, $user);
        }

        return false;
    }

    /**
     * @param Campaign $campaign
     * @param User|string $user
     * @return bool
     */
    protected function canEdit(Campaign $campaign, $user)
    {
        return $campaign->getUser() === $user && !$campaign->isFinished();
    }

    /**
     * @param Campaign $campaign
     * @param User|string $user
     * @return bool
     */
    protected function canView(Campaign $campaign, $user)
    {
        $campaignUser = $campaign->getUser();
        $campaignUserVisible = $campaignUser->getProfile()->getVisible();

        $isVisibilityAll = $campaignUserVisible === UserProfile::VISIBILITY_ALL;
        $isAllowedAsContact = $this->isAllowedAsContact($campaign, $user);
        $isAllowedAsRegistered = $this->isAllowedAsRegistered($campaign, $user);

        return $campaignUser === $user ||
            $isVisibilityAll ||
            $isAllowedAsRegistered ||
            $isAllowedAsContact;
    }

    /**
     * @param Campaign $campaign
     * @param User|string $user
     * @return bool
     */
    protected function canSeeDaysLeft(Campaign $campaign, $user)
    {
        return $campaign->getUser() === $user && $campaign->daysLeft();
    }

    /**
     * @param Campaign $campaign
     * @return bool
     */
    protected function canFund(Campaign $campaign)
    {
        return $campaign->getId() &&
            !$campaign->isFinished() &&
            !$campaign->isPaused() &&
            $campaign->isStarted() &&
            $campaign->getPercentOfFunded() < 1;
    }

    /**
     * @param Campaign $campaign
     * @return bool
     */
    protected function canShareFB(Campaign $campaign)
    {
        $profile = $campaign->getUser()->getProfile();

        return $profile->isVisibleForAll() && $profile->isFacebook() && $campaign->getId() && !$campaign->isFinished();
    }

    /**
     * @param Campaign $campaign
     * @return bool
     */
    protected function canShareTW(Campaign $campaign)
    {
        $profile = $campaign->getUser()->getProfile();

        return $profile->isVisibleForAll() && $profile->isTwitter() && $campaign->getId() && !$campaign->isFinished();
    }

    /**
     * @param Campaign $campaign
     * @param $user
     * @return bool
     */
    protected function canSeeSummary(Campaign $campaign, $user)
    {
        return $campaign->getUser() === $user;
    }


    /**
     * @param Campaign $campaign
     * @param $user
     * @return bool
     */
    private function isAllowedAsRegistered(Campaign $campaign, $user)
    {
        $campaignUser = $campaign->getUser();
        $campaignUserVisible = $campaignUser->getProfile()->getVisible();

        $isVisibilityRegistered = $campaignUserVisible === UserProfile::VISIBILITY_REGISTRED;
        $isRegisteredUser = $user instanceof User && $user->isCompleted();

        return $isVisibilityRegistered && $isRegisteredUser;
    }

    /**
     * @param Campaign $campaign
     * @param $user
     * @return bool
     */
    private function isAllowedAsContact(Campaign $campaign, $user)
    {
        $campaignUser = $campaign->getUser();
        $campaignUserVisible = $campaignUser->getProfile()->getVisible();

        $isVisibilityNon = $campaignUserVisible === UserProfile::VISIBILITY_NON;
        $isRegisteredUser = $user instanceof User && $user->isCompleted();

        $isContact = false;
        if ($isRegisteredUser && $campaignUser->hasContact($user)) {
            $userContact = $campaignUser->findContact($user);
            if ($campaignContact = $campaign->findContact($userContact)) {
                $isContact = $campaignContact->isConfirmedContact();
            }
        }

        return $isVisibilityNon && $isContact;
    }
}
