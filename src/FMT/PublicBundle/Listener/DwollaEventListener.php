<?php

declare(strict_types=1);

namespace FMT\PublicBundle\Listener;

use DateTime;
use FMT\DataBundle\Entity\DwollaEvent;
use FMT\DomainBundle\Repository\DwollaEventRepositoryInterface;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Webhook;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class DwollaEventListener
 */
class DwollaEventListener
{
    /**
     * @var DwollaEventRepositoryInterface
     */
    protected $dwollaEventRepository;

    /**
     * @param DwollaEventRepositoryInterface $dwollaEventRepository
     */
    public function __construct(DwollaEventRepositoryInterface $dwollaEventRepository)
    {
        $this->dwollaEventRepository = $dwollaEventRepository;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('_dwolla_webhook') || !$request->attributes->has('webhook')) {
            return;
        }

        /** @var Webhook $webhook */
        $webhook = $request->attributes->get('webhook');
        $dwollaEvent = $this->dwollaEventRepository->findById($webhook->getId());

        if ($dwollaEvent instanceof DwollaEvent) {
            $event->setController(function () {
                return new Response();
            });

            return;
        }

        $dwollaEvent = new DwollaEvent();
        $dwollaEvent
            ->setId($webhook->getId())
            ->setCreated($webhook->getCreated())
            ->setReceived(new DateTime())
            ->setTopic($webhook->getTopic())
            ->setResourceId($webhook->getResourceId())
        ;

        $this->dwollaEventRepository->add($dwollaEvent);
        $this->dwollaEventRepository->save($dwollaEvent);
    }
}
