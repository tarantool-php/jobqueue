<?php

namespace Tarantool\JobQueue\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tarantool\JobQueue\Listener\Event\RunnerFailedEvent;
use Tarantool\JobQueue\Listener\Event\RunnerIdleEvent;
use Tarantool\JobQueue\Listener\Event\TaskFailedEvent;
use Tarantool\JobQueue\Listener\Event\TaskProcessedEvent;

class LoggingListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::RUNNER_FAILED => 'onRunnerFailed',
            Events::RUNNER_IDLE => 'onRunnerIdle',
            Events::TASK_FAILED => ['onTaskFailed', -200],
            Events::TASK_PROCESSED => ['onTaskProcessed', -200],
        ];
    }

    public function onRunnerFailed(RunnerFailedEvent $event): void
    {
        $this->logger->critical($event->getError()->getMessage());
    }

    public function onRunnerIdle(RunnerIdleEvent $event): void
    {
        $this->logger->debug('Idling...');
    }

    public function onTaskFailed(TaskFailedEvent $event): void
    {
        $error = $event->getError();
        $task = $event->getTask();

        $this->logger->error(sprintf('Failed to process task #%d: %s',
            $task->getId(),
            $error->getMessage()),
            $task->getData()
        );
    }

    public function onTaskProcessed(TaskProcessedEvent $event): void
    {
        $task = $event->getTask();

        $this->logger->info(sprintf('Task #%d was successfully processed.',
            $task->getId()),
            $task->getData()
        );
    }
}
