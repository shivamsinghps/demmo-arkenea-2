<?php

namespace FMT\PublicBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class HandleStartedCampaignCommand
 * @package FMT\PublicBundle\Command
 */
class HandleFinishedCampaignCommand extends ContainerAwareCommand
{
    use LockableTrait;

    protected function configure()
    {
        $this
            ->setName('fmt:finished-campaign:handle')
            ->setDescription('Created order when finishing campaign')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }
        $output->writeln('Created order when finishing campaign');
        $campaignManager = $this->getContainer()->get('FMT\DomainBundle\Service\Manager\CampaignManager');
        $campaignManager->handleFinishedToday();
    }
}
