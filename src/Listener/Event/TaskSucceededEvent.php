<?php

namespace Tarantool\JobQueue\Listener\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

class TaskSucceededEvent extends Event
{
    use HasTask;
    use HasQueue;

    private $newTaskData = [];

    public function __construct(Task $task, Queue $queue)
    {
        $this->setTask($task);
        $this->setQueue($queue);
    }

    public function setNewTaskData(array $data): void
    {
        $this->newTaskData = $data;
    }

    public function getNewTaskData(): array
    {
        return $this->newTaskData;
    }

    public function getTaskData(): array
    {
        return $this->newTaskData ?: $this->getTask()->getData();
    }
}
