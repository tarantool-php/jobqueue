<?php

namespace Tarantool\JobQueue\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tarantool\JobQueue\JobOptions;
use Tarantool\Queue\TtlOptions;

class RecurrenceListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::TASK_PROCESSED => 'onTaskProcessed',
        ];
    }

    public function onTaskProcessed(TaskProcessedEvent $event): void
    {
        $task = $event->getTask();
        $queue = $event->getQueue();
        $data = $event->getTaskData();

        if (empty($data[JobOptions::RECURRENCE])) {
            return;
        }

        if ($newData = $event->getNewTaskData()) {
            $queue->put($newData, [TtlOptions::DELAY => $data[JobOptions::RECURRENCE]]);
            $queue->delete($task->getId());
        } else {
            $queue->release($task->getId(), [TtlOptions::DELAY => $data[JobOptions::RECURRENCE]]);
        }

        $event->stopPropagation();
    }
}
