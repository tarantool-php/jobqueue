<?php

namespace Tarantool\JobQueue;

use Tarantool\JobQueue\Handler\Handler;
use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

return new class extends DefaultConfigFactory
{
    public function createSuccessHandler(): Handler
    {
        return new class (parent::createSuccessHandler()) implements Handler {
            private $handler;

            public function __construct(Handler $handler)
            {
                $this->handler = $handler;
            }

            public function handle(Task $task, Queue $queue): void
            {
                $this->handler->handle($task, $queue);

                // do something extra here
            }
        };
    }
};
