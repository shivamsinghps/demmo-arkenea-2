<?php
/**
 * Author: Anton Orlov
 * Date: 10.05.2018
 * Time: 12:30
 */

namespace FMT\DomainBundle\Listener;

use FMT\DataBundle\Entity\UserTransaction;
use FMT\DomainBundle\Event\TransactionEvent;
use FMT\InfrastructureBundle\Helper\NotificationHelper;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DonateReceived implements EventSubscriberInterface
{
    /** @var EngineInterface */
    private $parser;

    public function __construct(EngineInterface $parser)
    {
        $this->parser = $parser;
    }

    public function onDonationReceived(TransactionEvent $event)
    {
        if ($event->getTransaction()->getType() !== UserTransaction::TXN_DONATION) {
            return;
        }

        $message = $this->parser->render("@DomainBundle/payment/donate_received.html.twig", [
            "transaction" => $event->getTransaction()
        ]);
        $recipient = $event->getTransaction()->getRecipient()->getProfile()->getEmail();

        NotificationHelper::submitFromTemplate($message, $recipient);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            TransactionEvent::TRANSACTION_COMPLETED => "onDonationReceived"
        ];
    }
}
