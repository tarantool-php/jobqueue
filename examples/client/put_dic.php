<?php

use Tarantool\JobQueue\JobOptions;

require __DIR__.'/../../vendor/autoload.php';

/** @var Tarantool\Queue\Queue $queue */
$queue = require __DIR__.'/queue.php';

$task = $queue->put([
    JobOptions::PAYLOAD => [
        JobOptions::PAYLOAD_SERVICE_ID => 'greet',
        JobOptions::PAYLOAD_SERVICE_ARGS => ['World'],
    ],
]);

printf("Added #%d: %s\n", $task->getId(), json_encode($task->getData()));
