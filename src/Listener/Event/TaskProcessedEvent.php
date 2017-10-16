<?php

namespace Tarantool\JobQueue\Listener\Event;

use Symfony\Component\EventDispatcher\Event;
use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

class TaskProcessedEvent extends Event
{
    use HasTask;
    use HasQueue;

    public function __construct(Task $task, Queue $queue)
    {
        $this->setTask($task);
        $this->setQueue($queue);
    }
}
