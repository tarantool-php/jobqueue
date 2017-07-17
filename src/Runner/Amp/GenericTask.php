<?php

namespace Tarantool\JobQueue\Runner\Amp;

use Amp\Parallel\Worker\Environment;
use Amp\Parallel\Worker\Task;
use Tarantool\JobQueue\Executor\Executor;
use Tarantool\JobQueue\Executor\ExecutorChain;
use Tarantool\JobQueue\Executor\ProcessExecutor;
use Tarantool\JobQueue\JobOptions;
use Tarantool\Queue\Queue;
use Tarantool\Queue\Task as QueueTask;

class GenericTask implements Task
{
    const EXECUTOR_KEY = self::class.'-executor';

    private $task;
    private $queue;
    private $executorsConfigFile;

    public function __construct(QueueTask $task, Queue $queue, string $executorsConfigFile = null)
    {
        $this->task = $task;
        $this->queue = $queue;
        $this->executorsConfigFile = $executorsConfigFile;
    }

    public function run(Environment $environment): void
    {
        $data = $this->task->getData();
        $payload = $data[JobOptions::PAYLOAD] ?? null;
        $executor = $this->getExecutor($environment);
        $executor->execute($payload, $this->queue);
    }

    private function getExecutor(Environment $environment): Executor
    {
        $executor = $environment->get(self::EXECUTOR_KEY);

        if (!$executor) {
            $executor = $this->createExecutor();
            $environment->set(self::EXECUTOR_KEY, $executor);
        }

        return $executor;
    }

    private function createExecutor(): Executor
    {
        if (!$this->executorsConfigFile) {
            return new ProcessExecutor();
        }

        $result = require $this->executorsConfigFile;
        if ($result instanceof Executor) {
            return $result;
        }

        if (!is_array($result)) {
            throw new \RuntimeException(sprintf('%s must return an Executor object or an array of Executor objects.', $this->executorsConfigFile));
        }

        $chain = new ExecutorChain();
        foreach ($result as $executor) {
            $chain->add($executor);
        }

        return $chain;
    }
}
