<?php

namespace Hgabka\KunstmaanEmailBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearMessageQueueCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('hgabka-kunstmaan-email:clear-message-queue')
            // the short description shown while running "php bin/console list"
            ->setDescription('Clears message queue')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Clears message queue')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time_start = microtime(true);

        $output->writeln('Clearing message queue...');

        $this->getContainer()->get('hgabka_kunstmaan_email.queue_manager')->clearMessageQueue();

        $output->writeln('Message queue cleared');
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $output->writeln("Execution time: $time seconds");
    }
}
