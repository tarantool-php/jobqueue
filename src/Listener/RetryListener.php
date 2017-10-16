<?php

namespace Tarantool\JobQueue\Listener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tarantool\JobQueue\Listener\Event\TaskFailedEvent;
use Tarantool\JobQueue\Listener\Event\TaskProcessedEvent;
use Tarantool\JobQueue\Listener\Event\TaskSucceededEvent;
use Tarantool\JobQueue\RetryStrategy\LimitedRetryStrategy;
use Tarantool\JobQueue\RetryStrategy\RetryStrategyFactory;
use Tarantool\JobQueue\JobOptions;
use Tarantool\Queue\TtlOptions;

class RetryListener implements EventSubscriberInterface
{
    private $retryStrategyFactory;

    private static $defaults = [
        JobOptions::RETRY_LIMIT => 2,
        JobOptions::RETRY_ATTEMPT => 1,
        JobOptions::RETRY_STRATEGY => RetryStrategyFactory::LINEAR,
    ];

    public function __construct(RetryStrategyFactory $retryStrategyFactory)
    {
        $this->retryStrategyFactory = $retryStrategyFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::TASK_FAILED => 'onTaskFailed',
            Events::TASK_SUCCEEDED => 'onTaskSucceeded',
        ];
    }

    public function onTaskFailed(TaskFailedEvent $event, string $eventName, EventDispatcherInterface $eventDispatcher): void
    {
        $task = $event->getTask();
        $data = $task->getData() + self::$defaults;
        $attempt = $data[JobOptions::RETRY_ATTEMPT];

        $strategy = $this->retryStrategyFactory->create($data[JobOptions::RETRY_STRATEGY]);
        $strategy = new LimitedRetryStrategy($strategy, $data[JobOptions::RETRY_LIMIT]);

        if (null === $delay = $strategy->getDelay($attempt)) {
            return;
        }

        $queue = $event->getQueue();

        // TODO replace these 2 calls with an atomic one
        $newTask = $queue->put([JobOptions::RETRY_ATTEMPT => $attempt + 1] + $data, [TtlOptions::DELAY => $delay]);
        $queue->delete($task->getId());

        $event->stopPropagation();

        $taskProcessedEvent = new TaskProcessedEvent($newTask, $queue);
        $eventDispatcher->dispatch(Events::TASK_PROCESSED, $taskProcessedEvent);
    }

    public function onTaskSucceeded(TaskSucceededEvent $event): void
    {
        $data = $event->getTaskData();
        unset($data[JobOptions::RETRY_ATTEMPT]);
        $event->setNewTaskData($data);
    }
}
