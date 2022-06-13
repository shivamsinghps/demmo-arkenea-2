<?php

namespace FMT\DataBundle\Entity;

/**
 * Interface ProductInterface
 * @package FMT\DataBundle\Entity
 */
interface ProductInterface extends EntityInterface
{
    /**
     * @return int
     */
    public function getPrice();

    /**
     * @return string
     */
    public function getSku();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @return bool
     */
    public function isAvailable();
}
