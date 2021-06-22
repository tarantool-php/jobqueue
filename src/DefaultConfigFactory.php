<?php

namespace Tarantool\JobQueue;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Psr\Log\LoggerInterface as Logger;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tarantool\Client\Client;
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
    private $clientOptions = [];
    private $logFile;
    private $logLevel = MonologLogger::DEBUG;
    private $executorsConfigFile;

    public function setQueueName(string $name): self
    {
        $this->queueName = $name;

        return $this;
    }

    public function setConnectionUri(string $uri): self
    {
        $this->clientOptions['uri'] = $uri;

        return $this;
    }

    public function getConnectionUri(): ?string
    {
        return $this->clientOptions['uri'] ?? null;
    }

    public function setCredentials(string $username, string $password): self
    {
        $this->clientOptions['username'] = $username;
        $this->clientOptions['password'] = $password;

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
        return Client::fromOptions($this->clientOptions);
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
