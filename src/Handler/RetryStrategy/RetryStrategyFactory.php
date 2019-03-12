<?php

namespace Tarantool\JobQueue\Handler\RetryStrategy;

use Tarantool\JobQueue\JobBuilder\RetryStrategies;

class RetryStrategyFactory
{
    public function create(string $strategy, array $args = []): RetryStrategy
    {
        switch ($strategy) {
            case RetryStrategies::CONSTANT: return new ConstantRetryStrategy(...$args);
            case RetryStrategies::EXPONENTIAL: return new ExponentialRetryStrategy(...$args);
            case RetryStrategies::LINEAR: return new LinearRetryStrategy(...$args);
        }

        throw new \InvalidArgumentException(sprintf('Unknown retry strategy "%s".', $strategy));
    }
}
