<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\BookstorePayment;

use FMT\InfrastructureBundle\Helper\NotificationHelper;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class EmailNotificator
 */
class EmailNotificator implements NotificatorInterface
{
    /**
     * @var string
     */
    protected $receiverEmail;

    /**
     * @var EngineInterface
     */
    protected $parser;

    /**
     * @param string          $receiverEmail
     * @param EngineInterface $parser
     */
    public function __construct(string $receiverEmail, EngineInterface $parser)
    {
        $this->receiverEmail = $receiverEmail;
        $this->parser = $parser;
    }

    /**
     * @inheritDoc
     */
    public function transferSend(SuccessTransfer $successTransfer): void
    {
        $message = $this->parser->render('@Domain/bookstore_payment/transfer_notification.html.twig', [
            'amount' => $successTransfer->getAmount(),
            'purchaseAmount' => $successTransfer->getPurchaseAmount(),
            'refundAmount' => $successTransfer->getRefundAmount(),
            'rejectedTransfersAmount' => $successTransfer->getRejectedTransfersAmount(),
        ]);

        NotificationHelper::submitFromTemplate($message, $this->receiverEmail);
    }
}
