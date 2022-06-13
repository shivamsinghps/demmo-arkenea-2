<?php

namespace FMT\PublicBundle\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class CommandVoter
 * @package FMT\PublicBundle\Voter
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CommandVoter extends Voter
{
    const CAN_RUN_BY_REQUEST = 'canRunByRequest';

    protected static $allPermissions = [
        self::CAN_RUN_BY_REQUEST,
    ];

    /** @var bool */
    private $isProd;

    /**
     * CronVoter constructor.
     * @param bool $isProd
     */
    public function __construct(bool $isProd)
    {
        $this->isProd = $isProd;
    }

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
        if (method_exists($this, $attribute)) {
            return $this->$attribute();
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function canRunByRequest()
    {
        return !$this->isProd;
    }
}
