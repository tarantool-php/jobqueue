<?php

namespace Tarantool\JobQueue\Executor;

use Tarantool\JobQueue\Executor\CallbackResolver\ContainerCallbackResolver;

$container = require __DIR__.'/container.php';

return [
    new CallbackExecutor(new ContainerCallbackResolver($container, 'job.'), $container['autowiring.job_args']),
    new ProcessExecutor(),
];
