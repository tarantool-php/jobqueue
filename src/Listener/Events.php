<?php

namespace Tarantool\JobQueue\Listener;

abstract class Events
{
    const RUNNER_FAILED = 'runner_failed';
    const RUNNER_IDLE = 'runner_idle';
    const TASK_FAILED = 'task_failed';
    const TASK_PROCESSED = 'task_processed';
}
