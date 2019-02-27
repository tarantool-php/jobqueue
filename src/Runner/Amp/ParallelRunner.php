<?php

namespace Tarantool\JobQueue\Runner\Amp;

use Amp\Loop;
use Amp\Parallel\Worker\DefaultPool;
use Psr\Log\LoggerInterface as Logger;
use Tarantool\JobQueue\Handler\Handler;
use Tarantool\JobQueue\Runner\Runner;
use Tarantool\Queue\Queue;

class ParallelRunner implements Runner
{
    private $queue;
    private $successHandler;
    private $failureHandler;
    private $logger;
    private $executorsConfigFile;

    public function __construct(Queue $queue, Handler $successHandler, Handler $failureHandler, Logger $logger, string $executorsConfigFile = null)
    {
        $this->queue = $queue;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->logger = $logger;
        $this->executorsConfigFile = $executorsConfigFile;
    }

    public function run(int $idleTimeout = 1): void
    {
        Loop::setErrorHandler(function (\Throwable $e) {
            $this->logger->critical($e->getMessage());
            throw $e;
        });

        Loop::run(function() use ($idleTimeout) {
            $pool = new DefaultPool();

            Loop::repeat(100, function () use ($pool, $idleTimeout) {
                if (!$queueTask = $this->queue->take($idleTimeout)) {
                    $this->logger->debug('Idling...');

                    return;
                }

                try {
                    $workerTask = new GenericTask($queueTask, $this->queue, $this->executorsConfigFile);
                    yield $pool->enqueue($workerTask);

                    $this->successHandler->handle($queueTask, $this->queue);
                    $this->logger->info(sprintf('Task #%d was successfully executed.', $queueTask->getId()), $queueTask->getData());
                } catch (\Throwable $e) {
                    $this->failureHandler->handle($queueTask, $this->queue);
                    $this->logger->error(sprintf('Failed to execute task #%d: %s', $queueTask->getId(), $e->getMessage()), $queueTask->getData());
                }
            });
        });
    }
}
