<?php

namespace Tarantool\JobQueue\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tarantool\JobQueue\Listener\Event\TaskFailedEvent;
use Tarantool\JobQueue\Listener\Event\TaskProcessedEvent;

class DefaultListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::TASK_FAILED => ['onTaskFailed', -100],
            Events::TASK_PROCESSED => ['onTaskProcessed', -100],
        ];
    }

    public function onTaskFailed(TaskFailedEvent $event): void
    {
        $task = $event->getTask();
        $queue = $event->getQueue();

        $queue->bury($task->getId());

        $event->stopPropagation();
    }

    public function onTaskProcessed(TaskProcessedEvent $event): void
    {
        $task = $event->getTask();
        $queue = $event->getQueue();

        if ($newData = $event->getNewTaskData()) {
            $queue->put($newData);
            $queue->delete($task->getId());
        } else {
            $queue->ack($task->getId());
        }

        $event->stopPropagation();
    }
}
