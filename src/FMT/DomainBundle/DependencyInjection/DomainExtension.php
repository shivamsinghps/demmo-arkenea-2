<?php

declare(strict_types=1);

namespace FMT\DomainBundle\DependencyInjection;

use FMT\DomainBundle\Service\BookstorePayment\SendTime;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Class DomainExtension
 * @package FMT\DomainBundle\DependencyInjection
 */
class DomainExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $sendTimeDefinition = new Definition(SendTime::class, [
            '$pause' => $config['bookstore_payment']['pause'],
            '$sendDate' => $config['bookstore_payment']['send_date'],
            '$sendTime' => $config['bookstore_payment']['send_time'],
            '$sendTimezone' => $config['bookstore_payment']['send_timezone'],
            '$errorTime' => $config['bookstore_payment']['error_time'],
        ]);
        $container->setDefinition(SendTime::class, $sendTimeDefinition);

        $container->setParameter('fmt.order.returns.return_window', $config['order']['returns']['return_window']);
        $container->setParameter('fmt.order.returns.chunk_size', $config['order']['returns']['chunk_size']);
        $container->setParameter('fmt.order.monitor.chunk_size', $config['order']['monitor']['chunk_size']);
    }
}
