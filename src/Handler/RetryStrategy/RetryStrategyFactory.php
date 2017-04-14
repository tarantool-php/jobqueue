<?php

namespace Tarantool\JobQueue\Handler\RetryStrategy;

class RetryStrategyFactory
{
    const CONSTANT = 'constant';
    const EXPONENTIAL = 'exponential';
    const LINEAR = 'linear';

    public function create(string $strategy, array $args = []): RetryStrategy
    {
        switch ($strategy) {
            case self::CONSTANT: return new ConstantRetryStrategy(...$args);
            case self::EXPONENTIAL: return new ExponentialRetryStrategy(...$args);
            case self::LINEAR: return new LinearRetryStrategy(...$args);
        }

        throw new \InvalidArgumentException(sprintf('Unknown retry strategy "%s".', $strategy));
    }
}
