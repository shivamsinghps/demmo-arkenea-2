<?php

namespace FMT\PublicBundle\Twig;

use FMT\DataBundle\Entity\CampaignBook;
use FMT\DomainBundle\Service\CartManagerInterface;
use FMT\DomainBundle\Type\Cart\Summary;

/**
 * Class CartExtension
 * @package FMT\PublicBundle\Twig
 */
class CartExtension extends \Twig_Extension
{
    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    public function __construct(CartManagerInterface $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('is_in_cart', [$this, 'isInCart']),
            new \Twig_SimpleFunction('cart_summary', [$this, 'cartSummary']),
        ];
    }

    /**
     * @param CampaignBook $book
     * @return bool
     */
    public function isInCart(CampaignBook $book)
    {
        return $this->cartManager->hasProduct($book);
    }

    /**
     * @return Summary
     */
    public function cartSummary()
    {
        return $this->cartManager->getSummary();
    }
}
