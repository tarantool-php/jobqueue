<?php

namespace Tarantool\JobQueue\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tarantool\JobQueue\JobOptions;
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

    public function onTaskSucceeded(TaskSucceededEvent $event): void
    {
        $data = $event->getTaskData();
        if (empty($data[JobOptions::RECURRENCE])) {
            return;
        }

        $task = $event->getTask();
        if (!$task->isTaken()) {
            return;
        }

        $queue = $event->getQueue();

        if ($newData = $event->getNewTaskData()) {
            $newTask = $queue->put($newData, [TtlOptions::DELAY => $data[JobOptions::RECURRENCE]]);
            $queue->delete($task->getId());
        } else {
            $newTask = $queue->release($task->getId(), [TtlOptions::DELAY => $data[JobOptions::RECURRENCE]]);
        }

        $event->setTask($newTask);
    }
}
