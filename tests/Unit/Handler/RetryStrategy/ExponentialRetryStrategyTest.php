<?php

namespace Tarantool\JobQueue\Tests\Unit\Handler\RetryStrategy;

use PHPUnit\Framework\TestCase;
use Tarantool\JobQueue\Handler\RetryStrategy\ExponentialRetryStrategy;

class ExponentialRetryStrategyTest extends TestCase
{
    /**
     * @dataProvider provideDelayData
     */
    public function testGetDelay(int $base, int $attempt, int $delay)
    {
        $strategy = new ExponentialRetryStrategy($base);

        $this->assertSame($delay, $strategy->getDelay($attempt));
    }

    public function provideDelayData()
    {
        return [
            [10, 1, 10],
            [10, 2, 100],
            [10, 3, 1000],
        ];
    }
}
