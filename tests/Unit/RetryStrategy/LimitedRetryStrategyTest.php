<?php

namespace Tarantool\JobQueue\Tests\Unit\RetryStrategy;

use PHPUnit\Framework\TestCase;
use Tarantool\JobQueue\RetryStrategy\LimitedRetryStrategy;
use Tarantool\JobQueue\RetryStrategy\RetryStrategy;

class LimitedRetryStrategyTest extends TestCase
{
    /**
     * @dataProvider provideDelayData
     */
    public function testGetDelay(int $retryLimit, int $attempt, int $mockDelay, ?int $resultDelay): void
    {
        $mock = $this->createMock(RetryStrategy::class);
        $mock->expects($this->any())->method('getDelay')
            ->with($attempt)
            ->willReturn($mockDelay);

        $strategy = new LimitedRetryStrategy($mock, $retryLimit);

        $this->assertSame($resultDelay, $strategy->getDelay($attempt));
    }

    public function provideDelayData(): array
    {
        return [
            [2, 1, 10, 10],
            [2, 2, 10, 10],
            [2, 3, 10, null],
        ];
    }
}
