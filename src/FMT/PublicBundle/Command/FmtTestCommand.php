<?php

namespace FMT\PublicBundle\Command;

use FMT\DataBundle\Entity\UserTransaction;
use FMT\DomainBundle\Event\TransactionEvent;
use FMT\DomainBundle\Repository\UserTransactionRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class FmtTestCommand
 * @package FMT\PublicBundle\Command
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FmtTestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fmt:test')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UserTransactionRepositoryInterface $factory */
        $repository = $this->getContainer()->get('FMT\DomainBundle\Repository\UserTransactionRepositoryInterface');
        /** @var UserTransaction $transaction */
        $transaction = $repository->findById(40);

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->getContainer()->get('Symfony\Component\EventDispatcher\EventDispatcherInterface');
//        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $event = new TransactionEvent($transaction);
        $dispatcher->dispatch(TransactionEvent::TRANSACTION_COMPLETED, $event);
    }
}
