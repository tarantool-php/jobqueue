<?php

namespace Tarantool\JobQueue\Listener\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tarantool\Queue\Queue;

class RunnerFailedEvent extends Event
{
    use HasError;
    use HasQueue;

    public function __construct(\Throwable $error, Queue $queue)
    {
        $this->setError($error);
        $this->setQueue($queue);
    }
}
