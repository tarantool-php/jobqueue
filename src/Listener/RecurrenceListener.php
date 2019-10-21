<?php

namespace Tarantool\JobQueue\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tarantool\JobQueue\JobBuilder\JobOptions;
use Tarantool\JobQueue\Listener\Event\TaskFailedEvent;
use Tarantool\JobQueue\Listener\Event\TaskSucceededEvent;
use Tarantool\Queue\Options;

class RecurrenceListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::TASK_SUCCEEDED => 'onTaskSucceeded',
            Events::TASK_FAILED => 'onTaskFailed',
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
            $newTask = $queue->put($newData, [Options::DELAY => $data[JobOptions::RECURRENCE]]);
            $queue->delete($task->getId());
        } else {
            $newTask = $queue->release($task->getId(), [Options::DELAY => $data[JobOptions::RECURRENCE]]);
        }

        $event->setTask($newTask);
    }

    public function onTaskFailed(TaskFailedEvent $event): void
    {
        $task = $event->getTask();
        if (!$task->isTaken()) {
            return;
        }

        $data = $task->getData();
        if (empty($data[JobOptions::RECURRENCE])) {
            return;
        }

        // do not bury recurrent tasks
        $queue = $event->getQueue();
        $newTask = $queue->release($task->getId(), [Options::DELAY => $data[JobOptions::RECURRENCE]]);
        $event->setTask($newTask);
    }
}
