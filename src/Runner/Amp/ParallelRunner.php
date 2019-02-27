<?php

namespace Tarantool\JobQueue\Runner\Amp;

use Amp\Loop;
use Amp\Parallel\Worker\DefaultPool;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Tarantool\JobQueue\Listener\Event\RunnerFailedEvent;
use Tarantool\JobQueue\Listener\Event\RunnerIdleEvent;
use Tarantool\JobQueue\Listener\Event\TaskFailedEvent;
use Tarantool\JobQueue\Listener\Event\TaskSucceededEvent;
use Tarantool\JobQueue\Listener\Events;
use Tarantool\JobQueue\Runner\Runner;
use Tarantool\Queue\Queue;

class ParallelRunner implements Runner
{
    private $queue;
    private $eventDispatcher;
    private $executorsConfigFile;

    public function __construct(Queue $queue, EventDispatcher $eventDispatcher, string $executorsConfigFile = null)
    {
        $this->queue = $queue;
        $this->eventDispatcher = $eventDispatcher;
        $this->executorsConfigFile = $executorsConfigFile;
    }

    public function run(int $idleTimeout = 1): void
    {
        Loop::setErrorHandler(function (\Throwable $error) {
            $event = new RunnerFailedEvent($error, $this->queue);
            $this->eventDispatcher->dispatch(Events::RUNNER_FAILED, $event);
            throw $error;
        });

        Loop::run(function() use ($idleTimeout) {
            $pool = new DefaultPool();

            Loop::repeat(100, function () use ($pool, $idleTimeout) {
                if (!$queueTask = $this->queue->take($idleTimeout)) {
                    $event = new RunnerIdleEvent($this->queue);
                    $this->eventDispatcher->dispatch(Events::RUNNER_IDLE, $event);

                    return;
                }

                try {
                    $workerTask = new GenericTask($queueTask, $this->queue, $this->executorsConfigFile);
                    yield $pool->enqueue($workerTask);

                    $event = new TaskSucceededEvent($queueTask, $this->queue);
                    $this->eventDispatcher->dispatch(Events::TASK_SUCCEEDED, $event);
                } catch (\Throwable $error) {
                    $event = new TaskFailedEvent($error, $queueTask, $this->queue);
                    $this->eventDispatcher->dispatch(Events::TASK_FAILED, $event);
                }
            });
        });
    }
}
