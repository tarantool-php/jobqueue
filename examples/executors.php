<?php

namespace Tarantool\JobQueue\Executor;

use Pimple\Psr11\Container;
use Tarantool\JobQueue\Executor\CallbackResolver\ContainerCallbackResolver;
use Tarantool\JobQueue\Executor\CallbackResolver\DirectCallbackResolver;

$container = require __DIR__.'/container.php';

$callback = function ($payload) use ($container) {
    $container['logger']->info(strrev($payload));
};

return [
    new CallbackExecutor(new DirectCallbackResolver($callback)),
    new CallbackExecutor(new ContainerCallbackResolver(new Container($container), 'job.'), $container['autowiring.job_args']),
    new ProcessExecutor(),
];
