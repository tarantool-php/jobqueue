<?php

namespace Tarantool\JobQueue\Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Tarantool\JobQueue\DefaultConfigFactory;

class Command extends BaseCommand
{
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT = 3301;
    const ENV_USER = 'TNT_JOBQUEUE_USER';
    const ENV_PASSWORD = 'TNT_JOBQUEUE_PASSWORD';

    private $configFactory;

    protected function configure(): void
    {
        $this
            ->addArgument('queue', InputArgument::REQUIRED)
            ->addOption('host', 'H', InputOption::VALUE_REQUIRED, '', self::DEFAULT_HOST)
            ->addOption('port', 'p', InputOption::VALUE_REQUIRED, '', self::DEFAULT_PORT)
            ->addOption('user', 'u', InputOption::VALUE_REQUIRED, '')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, '')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $customConfigPath = $input->getOption('config');
        if (null !== $customConfigPath && !is_readable($customConfigPath)) {
            throw new \RuntimeException("The given configuration file '$customConfigPath' does not exist or it's not readable.");
        }

        $this->configFactory = $customConfigPath ? include $customConfigPath : new DefaultConfigFactory();

        $uri = sprintf('tcp://%s:%s', $input->getOption('host'), $input->getOption('port'));
        $this->configFactory->setConnectionUri($uri);

        $queueName = $input->getArgument('queue');
        $this->configFactory->setQueueName($queueName);

        $user = $input->getOption('user') ?: getenv(self::ENV_USER);

        if ($user) {
            if (!$password = getenv(self::ENV_PASSWORD)) {
                $helper = $this->getHelper('question');
                $question = new Question('Password: ');
                $question->setHidden(true);
                $question->setHiddenFallback(false);
                $password = $helper->ask($input, $output, $question);
            }

            $this->configFactory->setCredentials($user, $password);
        }
    }

    protected function getConfigFactory(): DefaultConfigFactory
    {
        return $this->configFactory;
    }
}
