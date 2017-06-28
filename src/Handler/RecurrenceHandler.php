<?php

namespace Tarantool\JobQueue\Handler;

use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

class RecurrenceHandler implements Handler
{
    private $handler;

    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    public function handle(Task $task, Queue $queue): void
    {
        $data = $task->getData();

        if (empty($data['recurrence'])) {
            $this->handler->handle($task, $queue);

            return;
        }

        $queue->release($task->getId(), ['delay' => $data['recurrence']]);
    }
}
