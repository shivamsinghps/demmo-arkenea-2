<?php

namespace FMT\PublicBundle\Twig;

use FMT\DataBundle\Entity\User;
use FMT\PublicBundle\Controller\Student\ProfileController as StudentProfileController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class FlashExtension
 * @package FMT\PublicBundle\Twig
 */
class FlashExtension extends \Twig_Extension
{
    const SUCCESS_TYPE = 'success';
    const ERROR_TYPE = 'error';
    const INFO_TYPE = 'info';
    const WARNING_TYPE = 'warning';

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * FlashExtension constructor.
     * @param FlashBagInterface $flashBag
     */
    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_flash_messages', [$this, 'getFlashMessages']),
            new \Twig_SimpleFunction('clear_messages', [$this, 'clearMessages']),
        ];
    }

    /**
     * @param null $type
     * @return array
     */
    public function getFlashMessages($type = null)
    {
        if ($type) {
            return $this->flashBag->get($type);
        }

        return $this->flashBag->all();
    }

    /**
     * @param null $type
     */
    public function clearMessages($type = null)
    {
        if ($type) {
            $flashes = $this->flashBag->all();
            unset($flashes[$type]);
            $this->flashBag->setAll($flashes);

            return;
        }

        $this->flashBag->clear();
    }
}
