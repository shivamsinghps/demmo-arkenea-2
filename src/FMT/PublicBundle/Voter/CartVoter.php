<?php

namespace FMT\PublicBundle\Voter;

use FMT\DataBundle\Entity\CampaignProductInterface;
use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Service\CartManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class CartVoter
 * @package FMT\PublicBundle\Voter
 */
class CartVoter extends Voter
{
    const CAN_ADD_TO_CART = 'canAddToCart';
    const CAN_REMOVE_FROM_CART = 'canRemoveFromCart';

    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * CartVoter constructor.
     * @param CartManagerInterface $cartManager
     */
    public function __construct(CartManagerInterface $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [
            self::CAN_ADD_TO_CART,
            self::CAN_REMOVE_FROM_CART,
        ])) {
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

        if (method_exists($this, $attribute)) {
            return $this->$attribute($user, $subject);
        }

        return false;
    }

    /**
     * @param User|string $user
     * @param CampaignProductInterface $product
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function canAddToCart($user, CampaignProductInterface $product)
    {
        return $this->cartManager->canAddProduct($product);
    }

    /**
     * @param User|string $user
     * @param CampaignProductInterface $product
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function canRemoveFromCart($user, CampaignProductInterface $product)
    {
        return $this->cartManager->hasProduct($product);
    }
}
