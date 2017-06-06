<?php

namespace Tarantool\JobQueue\Console\Command;

use Monolog\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    const DEFAULT_IDLE_TIMEOUT = 1;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('run')
            ->setDescription('Runs a job worker')
            ->addOption('executors-config', 'e', InputOption::VALUE_REQUIRED, '')
            ->addOption('idle-timeout', 'i', InputOption::VALUE_REQUIRED, '', self::DEFAULT_IDLE_TIMEOUT)
            ->addOption('log-file', 'f', InputOption::VALUE_REQUIRED, '')
            ->addOption('log-level', 'l', InputOption::VALUE_REQUIRED, '', Logger::INFO)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = $this->createQueue($input, $output);
        $logger = $this->createLogger($queue->getName(), $input);

        if ($executorsConfigFile = $input->getOption('executors-config')) {
            $executorsConfigFile = realpath($executorsConfigFile);
        }

        $runner = $this->getConfigFactory()->createRunner(
            $queue,
            $logger,
            $executorsConfigFile
        );

        $runner->run($input->getOption('idle-timeout'));
    }

    private function createLogger(string $queueName, InputInterface $input)
    {
        $logFile = $input->getOption('log-file');
        $logLevel = self::translateLogLevel($input->getOption('log-level'));

        return $this->getConfigFactory()->createLogger($queueName, $logFile, $logLevel);
    }

    private static function translateLogLevel($name)
    {
        // level is already translated to logger constant, return as-is
        if (is_int($name)) {
            return $name;
        }

        $levels = Logger::getLevels();
        $upper = strtoupper($name);

        if (!isset($levels[$upper])) {
            throw new \InvalidArgumentException("Provided logging level '$name' does not exist. Must be a valid monolog logging level.");
        }

        return $levels[$upper];
    }
}
