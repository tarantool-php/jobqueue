<?php

namespace Tarantool\JobQueue\Listener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tarantool\JobQueue\Listener\Event\TaskFailedEvent;
use Tarantool\JobQueue\Listener\Event\TaskProcessedEvent;
use Tarantool\JobQueue\Listener\Event\TaskSucceededEvent;

class DefaultListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::TASK_FAILED => ['onTaskFailed', -100],
            Events::TASK_SUCCEEDED => ['onTaskSucceeded', -100],
        ];
    }

    public function onTaskFailed(TaskFailedEvent $event, string $eventName, EventDispatcherInterface $eventDispatcher): void
    {
        $task = $event->getTask();
        $queue = $event->getQueue();

        $newTask = $queue->bury($task->getId());

        $event->stopPropagation();

        $taskProcessedEvent = new TaskProcessedEvent($newTask, $queue);
        $eventDispatcher->dispatch(Events::TASK_PROCESSED, $taskProcessedEvent);
    }

    public function onTaskSucceeded(TaskSucceededEvent $event, string $eventName, EventDispatcherInterface $eventDispatcher): void
    {
        $task = $event->getTask();
        $queue = $event->getQueue();

        $newTask = $queue->ack($task->getId());

        $event->stopPropagation();

        $taskProcessedEvent = new TaskProcessedEvent($newTask, $queue);
        $eventDispatcher->dispatch(Events::TASK_PROCESSED, $taskProcessedEvent);
    }
}
