<?php

namespace Tarantool\JobQueue\Handler;

use Tarantool\JobQueue\Handler\RetryStrategy\LimitedRetryStrategy;
use Tarantool\JobQueue\Handler\RetryStrategy\RetryStrategyFactory;
use Tarantool\JobQueue\JobOptions;
use Tarantool\Queue\Options;
use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

class RetryHandler implements Handler
{
    private $handler;
    private $retryStrategyFactory;

    private static $defaults = [
        JobOptions::RETRY_LIMIT => 2,
        JobOptions::RETRY_ATTEMPT => 1,
        JobOptions::RETRY_STRATEGY => RetryStrategyFactory::LINEAR,
    ];

    public function __construct(Handler $handler, RetryStrategyFactory $retryStrategyFactory)
    {
        $this->handler = $handler;
        $this->retryStrategyFactory = $retryStrategyFactory;
    }

    public function handle(Task $task, Queue $queue): void
    {
        $data = $task->getData() + self::$defaults;
        $attempt = $data[JobOptions::RETRY_ATTEMPT];

        $strategy = $this->retryStrategyFactory->create($data[JobOptions::RETRY_STRATEGY]);
        $strategy = new LimitedRetryStrategy($strategy, $data[JobOptions::RETRY_LIMIT]);

        if (null === $delay = $strategy->getDelay($attempt)) {
            $this->handler->handle($task, $queue);

            return;
        }

        // TODO replace these 2 calls with an atomic one
        $queue->put([JobOptions::RETRY_ATTEMPT => $attempt + 1] + $data, [Options::DELAY => $delay]);
        $queue->delete($task->getId());
    }
}
