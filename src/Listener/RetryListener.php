<?php

namespace Tarantool\JobQueue\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tarantool\JobQueue\Listener\Event\TaskFailedEvent;
use Tarantool\JobQueue\Listener\Event\TaskSucceededEvent;
use Tarantool\JobQueue\RetryStrategy\LimitedRetryStrategy;
use Tarantool\JobQueue\RetryStrategy\RetryStrategyFactory;
use Tarantool\JobQueue\JobOptions;
use Tarantool\Queue\Options;

class RetryListener implements EventSubscriberInterface
{
    private $factory;

    private static $defaults = [
        JobOptions::RETRY_LIMIT => 2,
        JobOptions::RETRY_ATTEMPT => 0,
        JobOptions::RETRY_STRATEGY => RetryStrategyFactory::LINEAR,
    ];

    public function __construct(RetryStrategyFactory $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::TASK_FAILED => 'onTaskFailed',
            Events::TASK_SUCCEEDED => 'onTaskSucceeded',
        ];
    }

    public function onTaskFailed(TaskFailedEvent $event): void
    {
        $task = $event->getTask();
        if (!$task->isTaken()) {
            return;
        }

        $data = $task->getData() + self::$defaults;
        $attempt = $data[JobOptions::RETRY_ATTEMPT];

        $strategy = $this->factory->create($data[JobOptions::RETRY_STRATEGY]);
        $strategy = new LimitedRetryStrategy($strategy, $data[JobOptions::RETRY_LIMIT]);

        if (null === $delay = $strategy->getDelay($attempt)) {
            return;
        }

        $queue = $event->getQueue();

        // TODO replace these 2 calls with an atomic one
        $newTask = $queue->put([JobOptions::RETRY_ATTEMPT => $attempt + 1] + $data, [Options::DELAY => $delay]);
        $queue->delete($task->getId());

        $event->setTask($newTask);
    }

    public function onTaskSucceeded(TaskSucceededEvent $event): void
    {
        $data = $event->getTaskData();

        if (empty($data[JobOptions::RETRY_ATTEMPT])) {
            return;
        }

        unset($data[JobOptions::RETRY_ATTEMPT]);
        $event->setNewTaskData($data);
    }
}
