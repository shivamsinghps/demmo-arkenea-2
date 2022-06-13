<?php

namespace FMT\DomainBundle\Service;

use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Entity\CampaignProductInterface;
use FMT\DomainBundle\Exception\CartConfigurationException;
use FMT\DomainBundle\Type\Cart\Summary;
use FMT\DomainBundle\Type\Payment\Donation;

/**
 * Interface CartManagerInterface
 * @package FMT\DomainBundle\Service
 */
interface CartManagerInterface
{
    /**
     * @return Order
     */
    public function get(): Order;

    public function delete();

    public function save();

    /**
     * @param CampaignProductInterface $product
     * @return Order
     */
    public function addProduct(CampaignProductInterface $product): Order;

    /**
     * @param CampaignProductInterface $product
     * @return bool
     */
    public function hasProduct(CampaignProductInterface $product): bool;

    /**
     * @param CampaignProductInterface $product
     * @return bool
     */
    public function canAddProduct(CampaignProductInterface $product): bool;

    /**
     * @param CampaignProductInterface $product
     * @return bool
     */
    public function removeProduct(CampaignProductInterface $product): bool;

    /**
     * @param CampaignProductInterface[]|array $products
     * @return Summary
     * @throws CartConfigurationException
     */
    public function estimate(array $products): Summary;

    /**
     * @param Order|null $cart
     * @return Summary
     */
    public function getSummary(Order $cart = null): Summary;

    /**
     * @param Donation $donation
     * @param Order $order
     * @return array
     */
    public function sendDonationOrder(Donation $donation, Order $order);

    /**
     * @param Order $order
     * @return mixed
     */
    public function sendOrder(Order $order);
}
