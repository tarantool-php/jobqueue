<?php

namespace Tarantool\JobQueue\Executor;

use Tarantool\JobQueue\Exception\BadPayloadException;
use Tarantool\Queue\Queue;

class ExecutorChain implements Executor
{
    /**
     * @var Executor[]
     */
    private $executors;

    public function __construct(array $executors = [])
    {
        foreach ($executors as $executor) {
            $this->add($executor);
        }
    }

    public function add(Executor $executor): self
    {
        $this->executors[] = $executor;

        return $this;
    }

    public function execute($payload, Queue $queue): void
    {
        foreach ($this->executors as $executor) {
            try {
                $executor->execute($payload, $queue);
            } catch (BadPayloadException $e) {
                // try next executor
            }
        }

        throw new BadPayloadException($payload);
    }
}
