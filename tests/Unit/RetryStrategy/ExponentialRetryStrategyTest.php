<?php

namespace Tarantool\JobQueue\Tests\Unit\RetryStrategy;

use PHPUnit\Framework\TestCase;
use Tarantool\JobQueue\RetryStrategy\ExponentialRetryStrategy;

final class ExponentialRetryStrategyTest extends TestCase
{
    /**
     * @dataProvider provideDelayData
     */
    public function testGetDelay(int $base, int $attempt, int $delay): void
    {
        $strategy = new ExponentialRetryStrategy($base);

        $this->assertSame($delay, $strategy->getDelay($attempt));
    }

    public function provideDelayData(): array
    {
        return [
            [10, 0, 10],
            [10, 1, 100],
            [10, 2, 1000],
        ];
    }
}
