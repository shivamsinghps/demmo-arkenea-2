<?php

namespace FMT\PublicBundle\Controller\Common;

use FMT\PublicBundle\Controller\AbstractBaseController;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;
use Psr\Log\LoggerInterface;

/**
 * Class StripeEventController
 * @package FMT\PublicBundle\Controller\Common
 */
class StripeEventController extends AbstractBaseController
{
    /**
     * @var string
     */
    private $stripeWebhookSignature;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * StripeEventController constructor.
     * @param $stripeWebhookSignature
     * @param LoggerInterface $logger
     */
    public function __construct($stripeWebhookSignature, LoggerInterface $logger)
    {
        $this->stripeWebhookSignature = $stripeWebhookSignature;
        $this->logger = $logger;
    }

    /**
     * @Route("/stripe/listen")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function listenEvent(Request $request)
    {
        $requestContent = $request->getContent();
        $signHeader = $request->headers->get('stripe-signature');
        try {
            $event = Webhook::constructEvent(
                $requestContent,
                $signHeader,
                $this->stripeWebhookSignature
            );
        } catch (UnexpectedValueException $exception) {
            $this->logger->info('Invalid stripe webhook data', [
                'request'         => $requestContent,
                'signatureHeader' => $signHeader,
            ]);

            return new Response('', Response::HTTP_BAD_REQUEST);
        } catch (SignatureVerificationException $exception) {
            $this->logger->info('Invalid stripe webhook signature', [
                'request'         => $requestContent,
                'signatureHeader' => $signHeader,
            ]);

            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        $this->logger->info('Valid webhook accepted', [
            'event'           => $event,
            'request'         => $requestContent,
            'signatureHeader' => $signHeader,
        ]);

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
