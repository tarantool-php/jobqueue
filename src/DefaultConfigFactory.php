<?php

namespace Tarantool\JobQueue;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Psr\Log\LoggerInterface as Logger;
use Psr\Log\NullLogger;
use Tarantool\Client\Client;
use Tarantool\Client\Connection\Retryable;
use Tarantool\Client\Connection\StreamConnection;
use Tarantool\Client\Packer\PurePacker;
use Tarantool\JobQueue\Handler\AckHandler;
use Tarantool\JobQueue\Handler\BuryHandler;
use Tarantool\JobQueue\Handler\Handler;
use Tarantool\JobQueue\Handler\RecurrenceHandler;
use Tarantool\JobQueue\Handler\RetryHandler;
use Tarantool\JobQueue\Handler\RetryStrategy\RetryStrategyFactory;
use Tarantool\JobQueue\Runner\Amp\ParallelRunner;
use Tarantool\JobQueue\Runner\Runner;
use Tarantool\Queue\Queue;

class DefaultConfigFactory
{
    public function createRunner(Queue $queue, Logger $logger, string $executorsConfigFile = null): Runner
    {
        return new ParallelRunner(
            $queue,
            $this->createSuccessHandler(),
            $this->createFailureHandler(),
            $logger,
            $executorsConfigFile
        );
    }

    public function createQueue(string $name, $client): Queue
    {
        return new Queue($client, $name);
    }

    public function createClient(string $uri)
    {
        $conn = new StreamConnection($uri);
        $conn = new Retryable($conn);

        return new Client($conn, new PurePacker());
    }

    public function createLogger(string $queueName, string $logFile = null, int $logLevel = null): Logger
    {
        if (!$logFile) {
            return new NullLogger();
        }

        return new MonologLogger("$queueName:worker", [new StreamHandler($logFile, $logLevel)]);
    }

    public function createSuccessHandler(): Handler
    {
        return new RecurrenceHandler(new AckHandler());
    }

    public function createFailureHandler(): Handler
    {
        return new RetryHandler(
            new BuryHandler(),
            $this->createRetryStrategyFactory()
        );
    }

    public function createRetryStrategyFactory(): RetryStrategyFactory
    {
        return new RetryStrategyFactory();
    }
}
