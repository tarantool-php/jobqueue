<?php

namespace Tarantool\JobQueue\Listener\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

class TaskFailedEvent extends Event
{
    use HasError;
    use HasTask;
    use HasQueue;

    public function __construct(\Throwable $error, Task $task, Queue $queue)
    {
        $this->setError($error);
        $this->setTask($task);
        $this->setQueue($queue);
    }
}
