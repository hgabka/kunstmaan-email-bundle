<?php

namespace Hgabka\KunstmaanEmailBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendMessagesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('hgabka-kunstmaan-email:send-messages')

            // the short description shown while running "php bin/console list"
            ->setDescription('Sends messages in queue')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Sends out messages from the queue')
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_REQUIRED,
                'Maximum how many messages should be sent?',
                10
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time_start = microtime(true);

        $output->writeln('Sending starts...');

        $this->send($input->getOption('limit'), $output);

        $output->writeln('Sending done');
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $output->writeln("Execution time: $time seconds");
    }

    protected function send($limit, OutputInterface $output)
    {
        $output->writeln('Sending messages...');

        $result = $this
            ->getContainer()
            ->get('hgabka_kunstmaan_email.message_sender')
            ->sendMessageQueue($limit)
        ;
        $output->writeln(sprintf(
            'Total [%d] message(s) / success [%d] / failed [%d]',
            $result['total'],
            $result['sent'],
            $result['fail']
        ));
    }
}
