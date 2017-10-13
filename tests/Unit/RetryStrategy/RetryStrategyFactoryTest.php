<?php

namespace Tarantool\JobQueue\Tests\Unit\RetryStrategy;

use PHPUnit\Framework\TestCase;
use Tarantool\JobQueue\RetryStrategy\ConstantRetryStrategy;
use Tarantool\JobQueue\RetryStrategy\ExponentialRetryStrategy;
use Tarantool\JobQueue\RetryStrategy\LinearRetryStrategy;
use Tarantool\JobQueue\RetryStrategy\RetryStrategyFactory;

class RetryStrategyFactoryTest extends TestCase
{
    /**
     * @dataProvider provideCreateData
     */
    public function testCreate(string $expectedStrategyClass, string $strategyName, array $args = []): void
    {
        $factory = new RetryStrategyFactory();

        $strategy = func_num_args() === 2
            ? $factory->create($strategyName)
            : $factory->create($strategyName, $args);

        $this->assertInstanceOf($expectedStrategyClass, $strategy);
    }

    public function provideCreateData(): array
    {
        return [
            [ConstantRetryStrategy::class, RetryStrategyFactory::CONSTANT],
            [ExponentialRetryStrategy::class, RetryStrategyFactory::EXPONENTIAL],
            [LinearRetryStrategy::class, RetryStrategyFactory::LINEAR],
        ];
    }
}
