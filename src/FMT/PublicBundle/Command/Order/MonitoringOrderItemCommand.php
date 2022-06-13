<?php

namespace FMT\PublicBundle\Command\Order;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MonitoringOrderItemCommand
 * @package FMT\PublicBundle\Command\Order
 */
class MonitoringOrderItemCommand extends ContainerAwareCommand
{
    use LockableTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('fmt:order:monitoring_order_items')
            ->setDescription('Monitoring all order items. And if the entity has been changed, capture this.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process');

            return 0;
        }
        $output->writeln('Monitoring all order items. And if the entity has been changed, capture this.');
        $monitor = $this->getContainer()->get('FMT\DomainBundle\Service\Order\MonitorOrder');
        $monitor->monitor();

        return 0;
    }
}
