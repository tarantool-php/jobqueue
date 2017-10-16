<?php

namespace Tarantool\JobQueue\Listener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tarantool\JobQueue\JobOptions;
use Tarantool\JobQueue\Listener\Event\TaskProcessedEvent;
use Tarantool\JobQueue\Listener\Event\TaskSucceededEvent;
use Tarantool\Queue\TtlOptions;

class RecurrenceListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::TASK_SUCCEEDED => 'onTaskSucceeded',
        ];
    }

    public function onTaskSucceeded(TaskSucceededEvent $event, string $eventName, EventDispatcherInterface $eventDispatcher): void
    {
        $task = $event->getTask();
        $queue = $event->getQueue();
        $data = $event->getTaskData();

        if (empty($data[JobOptions::RECURRENCE])) {
            return;
        }

        if ($newData = $event->getNewTaskData()) {
            $newTask = $queue->put($newData, [TtlOptions::DELAY => $data[JobOptions::RECURRENCE]]);
            $queue->delete($task->getId());
        } else {
            $newTask = $queue->release($task->getId(), [TtlOptions::DELAY => $data[JobOptions::RECURRENCE]]);
        }

        $event->stopPropagation();

        $taskProcessedEvent = new TaskProcessedEvent($newTask, $queue);
        $eventDispatcher->dispatch(Events::TASK_PROCESSED, $taskProcessedEvent);
    }
}
