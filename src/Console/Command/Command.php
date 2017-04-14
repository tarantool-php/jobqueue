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
    const ENV_PASS = 'TNT_JOBQUEUE_PASS';

    private $configFactory;

    protected function addQueueConfiguration()
    {
        $this
            ->addArgument('queue', InputArgument::REQUIRED)
            ->addOption('host', 'H', InputOption::VALUE_REQUIRED, '', self::DEFAULT_HOST)
            ->addOption('port', 'p', InputOption::VALUE_REQUIRED, '', self::DEFAULT_PORT)
            ->addOption('user', 'u', InputOption::VALUE_REQUIRED, '')
        ;
    }

    protected function createQueue(InputInterface $input, OutputInterface $output)
    {
        $factory = $this->getConfigFactory();

        $uri = sprintf('tcp://%s:%s', $input->getOption('host'), $input->getOption('port'));
        $client = $factory->createClient($uri);

        $user = $input->getOption('user') ?: getenv(self::ENV_USER);

        if ($user) {
            if (!$password = getenv(self::ENV_PASS)) {
                $helper = $this->getHelper('question');
                $question = new Question('Password: ');
                $question->setHidden(true);
                $question->setHiddenFallback(false);
                $password = $helper->ask($input, $output, $question);
            }

            $client->authenticate($user, $password);
        }

        $queueName = $input->getArgument('queue');

        return $factory->createQueue($queueName, $client);
    }

    protected function getConfigFactory(): DefaultConfigFactory
    {
        if (!$this->configFactory) {
            $this->configFactory = new DefaultConfigFactory();
        }

        return $this->configFactory;
    }
}
