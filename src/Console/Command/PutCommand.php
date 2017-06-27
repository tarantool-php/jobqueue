<?php

namespace Tarantool\JobQueue\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PutCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('put')
            ->setDescription('Puts a task into the queue')
            ->addArgument('json-data', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $json = $input->getArgument('json-data');
        $queue = $this->createQueue($input, $output);

        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid json data.');
        }

        $task = $queue->put($data);

        $output->writeln(sprintf(
            '<comment>%s</comment> was successfully put to <info>%s</info> (#<comment>%d</comment>).',
            json_encode($task->getData()),
            $queue->getName(),
            $task->getId()
        ));
    }
}
