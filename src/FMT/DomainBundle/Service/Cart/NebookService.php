<?php

namespace FMT\DomainBundle\Service\Cart;

use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Repository\UserRepositoryInterface;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Client as ClientRest;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Client as ClientSoap;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Exception;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\CartSummary;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Shopper;

/**
 * Class NebookService
 * @package FMT\DomainBundle\Service\Cart
 */
class NebookService
{
    /**
     * @var ClientRest
     */
    private $clientRest;

    /**
     * @var ClientSoap
     */
    private $clientSoap;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * UserCartProvider constructor.
     * @param ClientRest $clientRest
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(ClientRest $clientRest, ClientSoap $clientSoap, UserRepositoryInterface $userRepository)
    {
        $this->clientRest = $clientRest;
        $this->clientSoap = $clientSoap;
        $this->userRepository = $userRepository;
    }

    /**
     * Get cart summary by requesting Nebook API for prices calculation
     *
     * @param Order $order
     * @return CartSummary
     * @throws Exception
     */
    public function getOrderSummary(Order $order)
    {
        $shopper = $this->getShopper($order->getCampaign()->getUser());

        // create and fill cart on Nebook side
        $items = NebookObjectHelper::getNebookCartItems($order);
        $this->clientRest->cartAddItems($shopper->getId(), $items);

        $codeId = $order->getCampaign()->getShippingOption();
        $shippingInfo = NebookObjectHelper::getNebookShippingInfo($shopper->getShippingAddress(), $codeId);

        // get Cart summary with needed prices
        $cartSummary = $this->clientRest->checkoutAddShipping($shopper->getId(), $shippingInfo);

        // clear test entities
        $this->clientRest->clearCart($shopper->getId());
        $this->clientRest->shopperDisableById($shopper->getId());

        return $cartSummary;
    }

    /**
     * @param User $user
     * @throws Exception
     * @return Shopper
     */
    public function createShopper(User $user)
    {
        // search Shopper by email
        $shopper = $this->clientRest->shopperGetByEmail($user->getEmail());
        $generatedShopper = NebookObjectHelper::makeShopper($user);
        if (empty($shopper)) {
            // create shopper on Nebook side
            $this->clientRest->shopperCreate($generatedShopper);
            $shopper = $this->clientRest->shopperGetByEmail($generatedShopper->getEmail());
        } else {
            // if such exists, update it with the current user's information
            $this->clientRest->shopperUpdate($shopper->getId(), $generatedShopper);
        }

        $user->setNebookId($shopper->getId());

        $this->userRepository->save($user);

        return $shopper;
    }

    /**
     * @param User $user
     * @throws Exception
     * @return Shopper
     */
    public function updateShopper(User $user)
    {
        $shopper = NebookObjectHelper::makeShopper($user);
        $this->clientRest->shopperUpdate($user->getNebookId(), $shopper);

        return $shopper;
    }

    /**
     * @param User $user
     * @return Shopper
     * @throws Exception
     */
    private function getShopper(User $user)
    {
        if (!$user->getNebookId()) {
            $shopper = $this->createShopper($user);
        } else {
            $shopper = $this->clientRest->shopperGetById($user->getNebookId());
        }

        return $shopper;
    }

    /**
     * @param Order $order
     * @throws Exception
     * @return int
     */
    public function getTaxAmount(Order $order)
    {
        $user = $order->getCampaign()->getUser();
        $region = $user->getProfile()->getAddress()->getRegion();

        $taxAmount = 0;
        $taxItems = $this->clientSoap->getTax();

        foreach ($taxItems as $taxItem) {
            if (strcmp($region, $taxItem->getState()) === 0) {
                $taxAmount = $taxItem->getAmount();
                break;
            }
        }

        return $taxAmount;
    }
}
