<?php

namespace Tarantool\JobQueue;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Psr\Log\LoggerInterface as Logger;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tarantool\Client\Client;
use Tarantool\Client\Connection\Retryable;
use Tarantool\Client\Connection\StreamConnection;
use Tarantool\Client\Packer\PurePacker;
use Tarantool\JobQueue\RetryStrategy\RetryStrategyFactory;
use Tarantool\JobQueue\Listener\DefaultListener;
use Tarantool\JobQueue\Listener\LoggingListener;
use Tarantool\JobQueue\Listener\RecurrenceListener;
use Tarantool\JobQueue\Listener\RetryListener;
use Tarantool\JobQueue\Runner\Amp\ParallelRunner;
use Tarantool\JobQueue\Runner\Runner;
use Tarantool\Queue\Queue;

class DefaultConfigFactory
{
    private $queueName;
    private $connectionUri;
    private $connectionOptions;
    private $username;
    private $password;
    private $logFile;
    private $logLevel;
    private $executorsConfigFile;

    public function setQueueName(string $name): self
    {
        $this->queueName = $name;

        return $this;
    }

    public function setConnectionUri(string $uri): self
    {
        $this->connectionUri = $uri;

        return $this;
    }

    public function setConnectionOptions(array $options): self
    {
        $this->connectionOptions = $options;

        return $this;
    }

    public function setCredentials(string $username, string $password): self
    {
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    public function setLogFile(string $logFile): self
    {
        $this->logFile = $logFile;

        return $this;
    }

    public function setLogLevel($logLevel): self
    {
        $this->logLevel = self::normalizeLogLevel($logLevel);

        return $this;
    }

    public function setExecutorsConfigFile(string $configFile): self
    {
        $this->executorsConfigFile = $configFile;

        return $this;
    }

    public function createRunner(): Runner
    {
        return new ParallelRunner(
            $this->createQueue(),
            $this->createEventDispatcher(),
            $this->executorsConfigFile
        );
    }

    public function createQueue(): Queue
    {
        $this->ensureQueueName();

        return new Queue($this->createClient(), $this->queueName);
    }

    public function createClient(): Client
    {
        if (!$this->connectionUri) {
            throw new \LogicException('Connection URI is not defined.');
        }

        $conn = new StreamConnection($this->connectionUri, $this->connectionOptions);
        $conn = new Retryable($conn);
        $client = new Client($conn, new PurePacker());

        if ($this->username) {
            // TODO make it lazy
            $client->authenticate($this->username, $this->password);
        }

        return $client;
    }

    public function createLogger(): Logger
    {
        if (!$this->logFile) {
            return new NullLogger();
        }

        $this->ensureQueueName();
        $handlers = [new StreamHandler($this->logFile, $this->logLevel)];

        return new MonologLogger("$this->queueName:worker", $handlers);
    }

    public function createEventDispatcher(): EventDispatcherInterface
    {
        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->addSubscriber(new DefaultListener());
        $eventDispatcher->addSubscriber(new RetryListener($this->createRetryStrategyFactory()));
        $eventDispatcher->addSubscriber(new RecurrenceListener());
        $eventDispatcher->addSubscriber(new LoggingListener($this->createLogger()));

        return $eventDispatcher;
    }

    public function createRetryStrategyFactory(): RetryStrategyFactory
    {
        return new RetryStrategyFactory();
    }

    private function ensureQueueName(): void
    {
        if (!$this->queueName) {
            throw new \LogicException('Queue name is not defined.');
        }
    }

    private static function normalizeLogLevel($name): int
    {
        // level is already translated to logger constant, return as-is
        if (is_int($name)) {
            return $name;
        }

        $levels = MonologLogger::getLevels();
        $upper = strtoupper($name);

        if (!isset($levels[$upper])) {
            throw new \InvalidArgumentException("Provided logging level '$name' does not exist. Must be a valid monolog logging level.");
        }

        return $levels[$upper];
    }
}
