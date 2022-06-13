<?php

namespace FMT\DomainBundle\Listener;

use FMT\DataBundle\Entity\Logs\LogOrderItem;
use FMT\DataBundle\Entity\OrderItem;

/**
 * Class LoggableListener
 * @package FMT\DomainBundle\Listener
 */
class LoggableListener extends \Gedmo\Loggable\LoggableListener
{
    protected function prePersistLogEntry($logEntry, $object)
    {
        if ($object instanceof OrderItem && $logEntry instanceof LogOrderItem) {
            if ($logEntry->getAction() !== 'remove') {
                $object->addLog($logEntry);
                if (is_array($logEntry->getData()) && array_key_exists('status', $logEntry->getData())) {
                    $logEntry->setStatus($logEntry->getData()['status']);
                }
            }
        }
    }
}
