<?php

namespace FMT\PublicBundle\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class MarketingExtension
 * @package FMT\PublicBundle\Twig
 */
class MarketingExtension extends \Twig_Extension
{
    const FOOTER_ORIENTATION = 'bottom:';
    const HEADER_ORIENTATION = 'top:';

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * MarketingExtension constructor.
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('footer_links', [$this, 'getLinksForFooter']),
            new \Twig_SimpleFunction('header_links', [$this, 'getLinksForHeader']),
        ];
    }

    /**
     * @return array
     */
    public function getLinksForFooter()
    {
        return $this->getLinksByOrientation(self::FOOTER_ORIENTATION);
    }

    /**
     * @return array
     */
    public function getLinksForHeader()
    {
        return $this->getLinksByOrientation(self::HEADER_ORIENTATION);
    }

    /**
     * @param string $orientation
     * @return array
     */
    private function getLinksByOrientation(string $orientation = self::HEADER_ORIENTATION)
    {
        $menuLinks = $this->parameterBag->get('base_menu');
        $marketingAppUrl = trim($this->parameterBag->get('marketing_app_url'), '/');
        $links = [];
        foreach ($menuLinks as $page => $link) {
            if (strpos($link, $orientation) === false) {
                continue;
            }

            $formattedPage = trim(
                str_replace($orientation, '', $link),
                '/'
            );
            $links[$page] = sprintf('%s/%s', $marketingAppUrl, $formattedPage);
        }

        return $links;
    }
}
