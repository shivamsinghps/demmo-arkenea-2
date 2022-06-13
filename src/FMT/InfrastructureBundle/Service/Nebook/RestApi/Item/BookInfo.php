<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 17:47
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class BookInfo
{
    /** @var string */
    private $isbn;

    /** @var string */
    private $author;

    /** @var string */
    private $binding;

    /** @var string */
    private $copyright;

    /** @var string */
    private $edition;

    /** @var string */
    private $publisher;

    /**
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getBinding()
    {
        return $this->binding;
    }

    /**
     * @return string
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * @return string
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * @return string
     */
    public function getPublisher()
    {
        return $this->publisher;
    }
}
