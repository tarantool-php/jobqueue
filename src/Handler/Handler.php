<?php

namespace Tarantool\JobQueue\Handler;

use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

interface Handler
{
    public function handle(Task $task, Queue $queue): void;
}
