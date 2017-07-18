<?php

namespace Tarantool\JobQueue\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatsCommand extends Command
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('stats')
            ->setDescription('Shows statistical information about the queue')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $queue = $this->createConfigFactory($input, $output)->createQueue();
        $stats = $queue->stats();

        $output->writeln(sprintf('Queue: <options=bold>%s</>', $queue->getName()));
        $output->writeln('Tasks: '.self::buildLine($stats['tasks']));
        $output->writeln('Calls: '.self::buildLine($stats['calls']));
    }

    private static function buildLine(array $stats): string
    {
        $items = [];
        foreach ($stats as $name => $count) {
            $items[] = "<options=bold>$count</> $name";
        }

        return implode(', ', $items);
    }
}
