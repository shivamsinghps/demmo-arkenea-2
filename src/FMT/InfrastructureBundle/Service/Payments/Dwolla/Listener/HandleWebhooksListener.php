<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Listener;

use FMT\InfrastructureBundle\Helper\CaseHelper;
use FMT\InfrastructureBundle\Helper\LogHelper;
use FMT\InfrastructureBundle\Service\Mapper\Mapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Exception\AuthenticationFailedException;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Webhook;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class HandleWebhooksListener
 */
class HandleWebhooksListener
{
    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @var string
     */
    protected $webhooksRoute;

    /**
     * @var string
     */
    protected $webhooksToken;

    /**
     * @var string
     */
    protected $webhooksController;

    /**
     * @param Mapper $mapper
     * @param string $webhooksRoute
     * @param string $webhooksToken
     * @param string $webhooksController
     */
    public function __construct(
        Mapper $mapper,
        string $webhooksRoute,
        string $webhooksToken,
        string $webhooksController
    ) {
        $this->mapper = $mapper;
        $this->webhooksRoute = $webhooksRoute;
        $this->webhooksToken = $webhooksToken;
        $this->webhooksController = $webhooksController;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($this->webhooksRoute !== $request->getRequestUri() || !$request->isMethod('POST')) {
            return;
        }

        try {
            $signature = $request->headers->get('x-request-signature-Sha-256', '');
            $this->authenticate($signature, $request->getContent());

            $topic = $request->headers->get('x-dwolla-topic');
            $action = CaseHelper::toCase($topic, CaseHelper::SNAKE_CASE, CaseHelper::CAMEL_CASE);
            LogHelper::info([sprintf('Received webhook with topic "%s"', $topic)]);

            if (!method_exists($this->webhooksController, $action)) {
                $action = 'other';
            }

            $webhook = $this->mapper->map(json_decode($request->getContent(), true), Webhook::class);

            $request->attributes->set('_controller', $this->webhooksController . '::' . $action);
            $request->attributes->set('webhook', $webhook);
            $request->attributes->set('_dwolla_webhook', true);
            $event->stopPropagation();
        } catch (AuthenticationFailedException $e) {
            $event->setResponse(new Response(null, 403));
        }
    }

    /**
     * @param string $requestSignature
     * @param string $requestBody
     *
     * @throws AuthenticationFailedException
     */
    protected function authenticate(string $requestSignature, string $requestBody): void
    {
        $hmac = hash_hmac('sha256', $requestBody, $this->webhooksToken);

        if ($hmac !== $requestSignature) {
            throw new AuthenticationFailedException();
        }
    }
}
