<?php

namespace Tarantool\JobQueue\Tests\Unit\Handler\RetryStrategy;

use PHPUnit\Framework\TestCase;
use Tarantool\JobQueue\Handler\RetryStrategy\ConstantRetryStrategy;
use Tarantool\JobQueue\Handler\RetryStrategy\ExponentialRetryStrategy;
use Tarantool\JobQueue\Handler\RetryStrategy\LinearRetryStrategy;
use Tarantool\JobQueue\Handler\RetryStrategy\RetryStrategyFactory;

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
