<?php

namespace Tarantool\JobQueue\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EnqueueCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('enqueue')
            ->setDescription('Enqueues a job')
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

        $queue->put($data);

        $output->writeln(sprintf('<comment>%s</comment> was successfully enqueued to <info>%s</info>.', $json, $queue->getName()));
    }
}
