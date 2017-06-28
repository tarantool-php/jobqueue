<?php

namespace Tarantool\JobQueue\Handler;

use Tarantool\JobQueue\Handler\RetryStrategy\LimitedRetryStrategy;
use Tarantool\JobQueue\Handler\RetryStrategy\RetryStrategyFactory;
use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

class RetryHandler implements Handler
{
    private $handler;
    private $retryStrategyFactory;

    private static $defaults = [
        'retry_limit' => 2,
        'retry_attempt' => 1,
        'retry_strategy' => RetryStrategyFactory::LINEAR,
    ];

    public function __construct(Handler $handler, RetryStrategyFactory $retryStrategyFactory)
    {
        $this->handler = $handler;
        $this->retryStrategyFactory = $retryStrategyFactory;
    }

    public function handle(Task $task, Queue $queue): void
    {
        $data = $task->getData() + self::$defaults;
        $attempt = $data['retry_attempt'];

        $strategy = $this->retryStrategyFactory->create($data['retry_strategy']);
        $strategy = new LimitedRetryStrategy($strategy, $data['retry_limit']);

        if (null === $delay = $strategy->getDelay($attempt)) {
            $this->handler->handle($task, $queue);

            return;
        }

        // TODO replace these 2 calls with an atomic one
        $queue->put(['retry_attempt' => $attempt + 1] + $data, ['delay' => $delay]);
        $queue->delete($task->getId());
    }
}
