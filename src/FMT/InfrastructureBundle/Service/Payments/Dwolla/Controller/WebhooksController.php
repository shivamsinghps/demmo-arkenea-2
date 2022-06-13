<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Controller;

use FMT\DomainBundle\Service\BookstorePayment\TransferManager;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Webhook;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WebhooksController
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class WebhooksController
{
    /**
     * @var TransferManager
     */
    private $transferManager;

    /**
     * @param TransferManager $transferManager
     */
    public function __construct(TransferManager $transferManager)
    {
        $this->transferManager = $transferManager;
    }

    /**
     * @return Response
     */
    public function other(): Response
    {
        return new Response();
    }

    /**
     * @param Webhook $webhook
     *
     * @return Response
     */
    public function customerBankTransferFailed(Webhook $webhook): Response
    {
        $this->transferManager->transferRejected($webhook->getResourceId());

        return new Response();
    }

    /**
     * @param Webhook $webhook
     *
     * @return Response
     */
    public function customerTransferFailed(Webhook $webhook): Response
    {
        $this->transferManager->transferRejected($webhook->getResourceId());

        return new Response();
    }

    /**
     * @param Webhook $webhook
     *
     * @return Response
     */
    public function customerBankTransferCreationFailed(Webhook $webhook): Response
    {
        $this->transferManager->transferRejected($webhook->getResourceId());

        return new Response();
    }

    /**
     * @param Webhook $webhook
     *
     * @return Response
     */
    public function customerBankTransferCancelled(Webhook $webhook): Response
    {
        $this->transferManager->transferRejected($webhook->getResourceId());

        return new Response();
    }

    /**
     * @param Webhook $webhook
     *
     * @return Response
     */
    public function customerTransferCancelled(Webhook $webhook): Response
    {
        $this->transferManager->transferRejected($webhook->getResourceId());

        return new Response();
    }

    /**
     * @param Webhook $webhook
     *
     * @return Response
     */
    public function transferCancelled(Webhook $webhook): Response
    {
        $this->transferManager->transferRejected($webhook->getResourceId());

        return new Response();
    }

    /**
     * @param Webhook $webhook
     *
     * @return Response
     */
    public function customerBankTransferCompleted(Webhook $webhook): Response
    {
        $this->transferManager->transferCompleted($webhook->getResourceId());

        return new Response();
    }

    /**
     * @param Webhook $webhook
     *
     * @return Response
     */
    public function customerTransferCompleted(Webhook $webhook): Response
    {
        $this->transferManager->transferCompleted($webhook->getResourceId());

        return new Response();
    }

    /**
     * @param Webhook $webhook
     *
     * @return Response
     */
    public function transferCompleted(Webhook $webhook): Response
    {
        $this->transferManager->transferCompleted($webhook->getResourceId());

        return new Response();
    }
}
