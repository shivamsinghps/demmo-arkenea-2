<?php

namespace FMT\DomainBundle\Service;

use FMT\DataBundle\Entity\CampaignBook;
use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Entity\ProductInterface;
use FOS\UserBundle\Model\UserInterface;

/**
 * Interface CartProcessorInterface
 * @package FMT\DomainBundle\Service
 */
interface CartProcessorInterface
{
    /**
     * Can processor be applied to the cart
     *
     * @param Order $cart
     * @return mixed
     */
    public function supports(Order $cart);

    /**
     * Perform cart modifications (e.g. Price calculations)
     *
     * @param Order $cart
     * @return mixed
     */
    public function process(Order $cart);
}
