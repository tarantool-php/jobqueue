<?php

namespace Tarantool\JobQueue\Tests\Unit\Handler\RetryStrategy;

use PHPUnit\Framework\TestCase;
use Tarantool\JobQueue\Handler\RetryStrategy\ConstantRetryStrategy;

class ConstantRetryStrategyTest extends TestCase
{
    /**
     * @dataProvider provideDelayData
     */
    public function testGetDelay(int $interval, int $attempt, int $delay): void
    {
        $strategy = new ConstantRetryStrategy($interval);

        $this->assertSame($delay, $strategy->getDelay($attempt));
    }

    public function provideDelayData(): array
    {
        return [
            [10, 1, 10],
            [10, 2, 10],
            [10, 3, 10],
            [42, 4, 42],
        ];
    }
}
