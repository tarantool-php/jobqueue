<?php

namespace Tarantool\JobQueue\Console\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatsCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('stats')
            ->setDescription('Shows statistical information about a queue')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = $this->createQueue($input, $output);
        $stats = $queue->stats();

        $this->renderTasksTable($stats, $output);
        $this->renderCallsTable($stats, $output);
    }

    private function renderTasksTable(array $stats, OutputInterface $output)
    {
        $rightAligned = new TableStyle();
        $rightAligned->setPadType(STR_PAD_LEFT);

        $table = new Table($output);
        $table->setHeaders(['Tasks', 'Count']);
        $table->setColumnStyle(1, $rightAligned);

        foreach ($stats['tasks'] as $task => $count) {
            $table->addRow([$task, $count]);
        }

        $table->render();
    }

    private function renderCallsTable(array $stats, OutputInterface $output)
    {
        $rightAligned = new TableStyle();
        $rightAligned->setPadType(STR_PAD_LEFT);

        $table = new Table($output);
        $table->setHeaders(['Calls', 'Count']);
        $table->setColumnStyle(1, $rightAligned);

        foreach ($stats['calls'] as $task => $count) {
            $table->addRow([$task, $count]);
        }

        $table->render();
    }
}
