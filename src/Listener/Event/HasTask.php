<?php

namespace Tarantool\JobQueue\Listener\Event;

use Tarantool\Queue\Task;

trait HasTask
{
    private $task;

    public function setTask(Task $task): void
    {
        $this->task = $task;
    }

    public function getTask(): Task
    {
        return $this->task;
    }
}
