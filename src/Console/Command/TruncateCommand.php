<?php

namespace Tarantool\JobQueue\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TruncateCommand extends Command
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('truncate')
            ->setDescription('Deletes all tasks from the queue')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $queue = $this->getConfigFactory()->createQueue();
        $queue->truncate();

        $output->writeln(sprintf('<info>%s</info> was successfully truncated.', $queue->getName()));
    }
}
