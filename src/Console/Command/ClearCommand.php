<?php

namespace Tarantool\JobQueue\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCommand extends Command
{
    protected function configure()
    {
        $this->addQueueConfiguration();

        $this
            ->setName('clear')
            ->setDescription('Removes all jobs from a queue')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = $this->createQueue($input, $output);
        $queue->truncate();

        $output->writeln(sprintf('<info>%s</info> was successfully cleared.', $queue->getName()));
    }
}
