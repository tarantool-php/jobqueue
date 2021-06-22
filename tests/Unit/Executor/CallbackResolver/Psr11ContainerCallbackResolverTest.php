<?php

namespace Tarantool\JobQueue\Tests\Unit\Executor\CallbackResolver;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tarantool\JobQueue\Exception\BadPayloadException;
use Tarantool\JobQueue\Executor\CallbackResolver\Psr11ContainerCallbackResolver;
use Tarantool\JobQueue\JobBuilder\JobOptions;

final class Psr11ContainerCallbackResolverTest extends TestCase
{
    /**
     * @dataProvider provideResolveData
     */
    public function testResolve(string $serviceName, string $id, string $idFormat = null): void
    {
        $callback = static function ($payload) {};
        $payload = [JobOptions::PAYLOAD_SERVICE_ID => $serviceName];

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->atLeastOnce())->method('get')
            ->with($id)
            ->willReturn($callback);

        $executor = null !== $idFormat
            ? new Psr11ContainerCallbackResolver($container, $idFormat)
            : new Psr11ContainerCallbackResolver($container);

        $this->assertSame($callback, $executor->resolve($payload));
    }

    public function provideResolveData(): iterable
    {
        return [
            ['foobar', 'foobar'],
            ['foobar', 'foobar', '%s'],
            ['foobar', 'job.foobar', 'job.%s'],
            ['foobar', 'foobar.job', '%s.job'],
            ['foobar', 'jobqueue.foobar.job', 'jobqueue.%s.job'],
        ];
    }

    public function testResolveInvalidPayload(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $executor = new Psr11ContainerCallbackResolver($container);

        $this->expectException(BadPayloadException::class);

        $executor->resolve(['invalid_key' => 'foobar']);
    }
}
