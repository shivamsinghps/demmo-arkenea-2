<?php

declare(strict_types=1);

namespace FMT\PublicBundle\Command\Order;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckOrderReturnsCommand
 * @package FMT\PublicBundle\Command
 */
class CheckOrderReturnsCommand extends ContainerAwareCommand
{
    use LockableTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('fmt:order:check-returns')
            ->setDescription('Start checking order returns')
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
        $output->writeln('Start checking order returns');
        $orderReturnsChecker = $this->getContainer()->get('FMT\DomainBundle\Service\OrderReturnsCheckerInterface');
        $orderReturnsChecker->check();

        return 0;
    }
}
