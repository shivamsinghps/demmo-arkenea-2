<?php

namespace FMT\DomainBundle\Event;

use FMT\DataBundle\Entity\CampaignBook;
use Symfony\Component\EventDispatcher\Event;

class BookEvent extends Event
{
    const BOOK_UPDATED = "fmt.book_updated";
    const BOOK_FAILED = "fmt.book_failed";

    /** @var CampaignBook */
    private $book;

    /**
     * BookEvent constructor.
     * @param CampaignBook|null $book
     */
    public function __construct(CampaignBook $book = null)
    {
        $this->book = $book;
    }

    /**
     * @return CampaignBook
     */
    public function getBook()
    {
        return $this->book;
    }
}
