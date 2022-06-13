<?php

namespace FMT\PublicBundle\Twig;

use FMT\DataBundle\Entity\CampaignBook;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BookExtension
 * @package FMT\PublicBundle\Twig
 */
class BookExtension extends \Twig_Extension
{
    /** @var TranslatorInterface */
    private $translator;

    /**
     * BookExtension constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('book_state', [$this, 'bookStateFilter']),
        ];
    }

    /**
     * @param CampaignBook $book
     * @return string
     */
    public function bookStateFilter(CampaignBook $book)
    {
        if (is_numeric($book->getState())) {
            return $this->translator->trans(sprintf('fmt.statuses.book_state.%s', $book->getStateName()));
        }

        return ucfirst(strtolower($book->getState()));
    }
}
