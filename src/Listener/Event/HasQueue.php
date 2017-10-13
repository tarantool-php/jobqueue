<?php

namespace Tarantool\JobQueue\Listener;

use Tarantool\Queue\Queue;

trait HasQueue
{
    private $queue;

    private function setQueue(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function getQueue(): Queue
    {
        return $this->queue;
    }
}
