<?php

namespace Tarantool\JobQueue\Tests\Unit\RetryStrategy;

use PHPUnit\Framework\TestCase;
use Tarantool\JobQueue\RetryStrategy\LinearRetryStrategy;

class LinearRetryStrategyTest extends TestCase
{
    /**
     * @dataProvider provideDelayData
     */
    public function testGetDelay(int $step, int $attempt, int $delay): void
    {
        $strategy = new LinearRetryStrategy($step);

        $this->assertSame($delay, $strategy->getDelay($attempt));
    }

    public function provideDelayData(): array
    {
        return [
            [10, 0, 10],
            [10, 1, 20],
            [10, 2, 30],
        ];
    }
}
