<?php

namespace Tarantool\JobQueue\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tarantool\JobQueue\Listener\Event\TaskFailedEvent;
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

    public function onTaskFailed(TaskFailedEvent $event): void
    {
        $task = $event->getTask();

        if ($task->isTaken()) {
            $queue = $event->getQueue();
            $newTask = $queue->bury($task->getId());
            $event->setTask($newTask);
        }
    }

    public function onTaskSucceeded(TaskSucceededEvent $event): void
    {
        $task = $event->getTask();

        if ($task->isTaken()) {
            $queue = $event->getQueue();
            $newTask = $queue->ack($task->getId());
            $event->setTask($newTask);
        }
    }
}
