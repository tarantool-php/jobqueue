<?php

namespace Tarantool\JobQueue\Tests\Unit\Handler\RetryStrategy;

use PHPUnit\Framework\TestCase;
use Tarantool\JobQueue\Handler\RetryStrategy\LinearRetryStrategy;

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
            [10, 1, 10],
            [10, 2, 20],
            [10, 3, 30],
        ];
    }
}
