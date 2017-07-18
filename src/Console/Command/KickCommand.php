<?php

namespace Tarantool\JobQueue\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KickCommand extends Command
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('kick')
            ->setDescription('Kicks buried tasks back to the queue')
            ->addArgument('count', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $queue = $this->createConfigFactory($input, $output)->createQueue();
        $count = $input->getArgument('count');

        $affected = $queue->kick($count);

        $output->writeln(sprintf(
            '<comment>%d</comment> tasks were successfully kicked back to <info>%s</info>.',
            $affected,
            $queue->getName()
        ));
    }
}
