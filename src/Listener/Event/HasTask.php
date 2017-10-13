<?php

namespace Tarantool\JobQueue\Listener\Event;

use Tarantool\Queue\Task;

trait HasTask
{
    private $task;

    private function setTask(Task $task)
    {
        $this->task = $task;
    }

    public function getTask(): Task
    {
        return $this->task;
    }
}
