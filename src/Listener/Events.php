<?php

namespace Tarantool\JobQueue\Listener;

final class Events
{
    public const RUNNER_FAILED = 'runner_failed';
    public const RUNNER_IDLE = 'runner_idle';
    public const TASK_FAILED = 'task_failed';
    public const TASK_SUCCEEDED = 'task_succeeded';

    private function __construct()
    {
    }
}
