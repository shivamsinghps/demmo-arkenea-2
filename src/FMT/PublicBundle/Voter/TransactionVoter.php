<?php
/**
 * Author: Anton Orlov
 * Date: 16.05.2018
 * Time: 10:30
 */

namespace FMT\PublicBundle\Voter;

use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserTransaction;
use FMT\DomainBundle\Service\PaymentManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TransactionVoter extends Voter
{
    const CAN_VIEW_TRANSACTION = "VIEW_TRANSACTION";
    const CAN_THANKS = "CAN_THANKS";
    const CAN_VIEW_RECEIPT = "CAN_VIEW_RECEIPT";

    /** @var array */
    private static $methodMapping = [
        self::CAN_VIEW_TRANSACTION => "canViewTransaction",
        self::CAN_THANKS => "canThanks",
        self::CAN_VIEW_RECEIPT => "canViewReceipt",
    ];

    /** @var PaymentManagerInterface */
    private $manager;

    public function __construct(PaymentManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (!($subject instanceof UserTransaction)) {
            return false;
        }
        return array_key_exists($attribute, self::$methodMapping);
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param UserTransaction $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $owner = $token->getUser();

        if (!array_key_exists($attribute, self::$methodMapping)) {
            return false;
        }

        if (!($owner instanceof User)) {
            $owner = null;
        }

        return call_user_func([$this, self::$methodMapping[$attribute]], $subject, $owner);
    }

    /**
     * @param UserTransaction $transaction
     * @param User|null $owner
     * @return bool
     */
    protected function canViewTransaction(UserTransaction $transaction, User $owner = null)
    {
        if (!empty($owner) && $owner !== $transaction->getSender()) {
            return false;
        }

        if (empty($owner) && $transaction->getSender() && $transaction->getSender()->isRegistered()) {
            return false;
        }

        return true;
    }

    /**
     * @param UserTransaction $transaction
     * @param User|null $user
     * @return bool
     */
    protected function canThanks(UserTransaction $transaction, User $user = null)
    {
        $campaign = $transaction->getCampaign();
        if (empty($campaign)) {
            return false;
        }

        return $user === $campaign->getUser();
    }

    /**
     * @param UserTransaction $transaction
     * @param User|null $user
     * @return bool
     */
    protected function canViewReceipt(UserTransaction $transaction, User $user = null)
    {
        if ($transaction->getSender() == $user || $transaction->getRecipient() == $user) {
            return true;
        }

        return false;
    }
}
