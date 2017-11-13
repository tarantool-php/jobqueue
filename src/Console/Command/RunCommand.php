<?php

namespace Tarantool\JobQueue\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    private const DEFAULT_IDLE_TIMEOUT = 1;

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('run')
            ->setDescription('Runs a job worker')
            ->addOption('executors-config', 'e', InputOption::VALUE_REQUIRED)
            ->addOption('idle-timeout', 'i', InputOption::VALUE_REQUIRED, '', self::DEFAULT_IDLE_TIMEOUT)
            ->addOption('log-file', 'f', InputOption::VALUE_REQUIRED)
            ->addOption('log-level', 'l', InputOption::VALUE_REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $configFactory = $this->createConfigFactory($input, $output);

        if ($logFile = $input->getOption('log-file')) {
            $configFactory->setLogFile($logFile);
        }
        if ($logLevel = $input->getOption('log-level')) {
            $configFactory->setLogLevel($logLevel);
        }
        if ($executorsConfigFile = $input->getOption('executors-config')) {
            $configFactory->setExecutorsConfigFile(realpath($executorsConfigFile));
        }

        $runner = $configFactory->createRunner();
        $runner->run($input->getOption('idle-timeout'));
    }
}
