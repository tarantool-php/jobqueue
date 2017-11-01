<?php

namespace Tarantool\JobQueue\Executor;

use Pimple\Psr11\Container;
use Tarantool\JobQueue\Executor\CallbackResolver\DirectCallbackResolver;
use Tarantool\JobQueue\Executor\CallbackResolver\Psr11ContainerCallbackResolver;

$container = require __DIR__.'/container.php';

$callback = function ($payload) use ($container) {
    $container['logger']->info(strrev($payload));
};

return [
    new CallbackExecutor(new DirectCallbackResolver($callback)),
    new CallbackExecutor(new Psr11ContainerCallbackResolver(new Container($container), 'job.%s'), $container['autowired_job_args']),
    new ProcessExecutor(),
];
