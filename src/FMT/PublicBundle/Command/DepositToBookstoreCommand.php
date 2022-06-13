<?php

declare(strict_types=1);

namespace FMT\PublicBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DepositToBookstoreCommand
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DepositToBookstoreCommand extends ContainerAwareCommand
{
    use LockableTrait;

    protected function configure()
    {
        $this->setName('fmt:transfers:deposit_bookstore');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }
        $output->writeln('Run command DepositToBookstoreCommand');
        $paymentManager = $this->getContainer()->get('FMT\DomainBundle\Service\BookstorePaymentManagerInterface');
        $sendTime = $this->getContainer()->get('FMT\DomainBundle\Service\BookstorePayment\SendTime');

        $paymentManager->sendTransfer($sendTime, false);

        return 0;
    }
}
