<?php

namespace FMT\DomainBundle\Service\Cart;

use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Entity\User;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Address;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\CartItem;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ShippingInfo;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Shopper;

/**
 * Class NebookOrderHelper
 * @package FMT\DomainBundle\Service\Cart
 */
class NebookObjectHelper
{
    /**
     * @param User $user
     * @return Shopper
     */
    public static function makeShopper(User $user)
    {
        $profile = $user->getProfile();

        $address = new Address();
        $address->setAddress1($profile->getAddress()->getAddress1());
        $address->setAddress2($profile->getAddress()->getAddress2());
        $address->setCity($profile->getAddress()->getCity());
        $address->setCountry($profile->getAddress()->getCountry());
        $address->setState($profile->getAddress()->getRegion());
        $address->setZip($profile->getAddress()->getCode());
        $address->setFirstName($profile->getFirstName());
        $address->setLastName($profile->getLastName());
        $address->setPhone('1-555-555-5555');

        $shopper = new Shopper();
        $shopper->setAllowBuybackEmail(true);
        $shopper->setAllowDirectEmail(true);
        $shopper->setEmail($user->getEmail());
        $shopper->setDisabled(false);
        $shopper->setTaxExempt(false);
        $shopper->setPassword(substr($user->getPassword(), 0, 20));
        $shopper->setStudentId($profile->getStudentId());
        $shopper->setShippingAddress($address);

        return $shopper;
    }

    /**
     * @param Order $order
     * @return array
     */
    public static function getNebookCartItems(Order $order)
    {
        $items = [];

        foreach ($order->getItems() as $item) {
            $cartItem = new CartItem();
            $cartItem->setSku($item->getSku());
            $cartItem->setPrice($item->getPrice());
            $cartItem->setQuantity($item->getQuantity());
            $cartItem->setFamilyId($item->getBook()->getProductFamilyId());
            $cartItem->setAllowSubstitution(false);
            $cartItem->setRental(false);

            $items[] = $cartItem;
        }

        return $items;
    }

    /**
     * @param Address $address
     * @param int $shippingCode
     * @return ShippingInfo
     */
    public static function getNebookShippingInfo(Address $address, int $shippingCode)
    {
        $shippingInfo = new ShippingInfo();
        $shippingInfo->setAddress($address);
        $shippingInfo->setShippingCodeId($shippingCode);
        $shippingInfo->setInstructions('test');

        return $shippingInfo;
    }
}
