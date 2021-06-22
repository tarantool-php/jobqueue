<?php

namespace Tarantool\JobQueue\Tests\Unit\Executor;

use PHPUnit\Framework\TestCase;
use Tarantool\JobQueue\Executor\ProcessExecutor;
use Tarantool\Queue\Queue;

final class ProcessExecutorTest extends TestCase
{
    public function testExecute(): void
    {
        $executor = new ProcessExecutor();
        $queue = $this->createMock(Queue::class);

        $filename = sprintf('%s/%s.test', sys_get_temp_dir(), uniqid(str_replace('\\', '_', __CLASS__), true));
        self::assertFileDoesNotExist($filename);

        $executor->execute("php -r 'touch(\"$filename\");'", $queue);

        self::assertFileExists($filename);
        @unlink($filename);
    }
}
