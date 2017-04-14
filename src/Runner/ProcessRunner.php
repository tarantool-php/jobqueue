<?php

namespace Tarantool\JobQueue\Runner;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Process\Process;
use Tarantool\JobQueue\Executor\Executor;
use Tarantool\JobQueue\Handler\Handler;
use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

class ProcessRunner implements Runner
{
    private $executor;
    private $successHandler;
    private $failureHandler;
    private $queue;
    private $logger;
    private $poolSize;
    private $runningTasks = [];

    public function __construct(Executor $executor, Handler $successHandler, Handler $failureHandler, Queue $queue, Logger $logger, $poolSize)
    {
        $this->executor = $executor;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->queue = $queue;
        $this->logger = $logger;
        $this->poolSize = $poolSize;
    }

    public function run(int $idleTimeout = 1)
    {
        while (true) {
            $this->checkFinishedTasks();
            $this->startPendingTasks();

            $this->logger->debug(sprintf('Pool size: %d', count($this->runningTasks)));
            sleep($this->isPoolEmpty() ? $idleTimeout : 1);
        }
    }

    private function checkFinishedTasks()
    {
        foreach ($this->runningTasks as $taskId => $data) {
            $process = $data['process'];
            if ($process->isRunning()) {
                continue;
            }

            $this->logger->debug(sprintf('Task #%d is finished.', $taskId));

            $task = $data['task'];
            if (0 === $process->getExitCode()) {
                $this->successHandler->handle($task, $this->queue);
                $this->logger->info(sprintf('Task #%d was successfully executed.', $task->getId()), $task->getData());            } else {
                $this->failureHandler->handle($task, $this->queue);
                $this->logger->info(sprintf('Task #%d is failed: %s', $taskId, $process->getErrorOutput()));
            }

            unset($this->runningTasks[$taskId]);
        }
    }

    private function startPendingTasks()
    {
        while (count($this->runningTasks) < $this->poolSize) {
            if (!$task = $this->queue->take(.1)) {
                break;
            }

            $this->startTask($task);
        }
    }

    private function startTask(Task $task)
    {
        try {
            $process = new Process($task->getData()['payload']);
            $process->start();
        } catch (\Exception $e) {
            $this->failureHandler->handle($task, $this->queue);
            $this->logger->error(sprintf('Failed to run task #%d: %s', $task->getId(), $e->getMessage()));

            return;
        }

        $this->runningTasks[$task->getId()] = [
            'task' => $task,
            'process' => $process,
        ];

        $this->logger->info(sprintf('Running the task #%d.', $task->getId()));
    }

    private function isPoolEmpty()
    {
        return empty($this->runningTasks);
    }
}
