<?php

require __DIR__.'/../../vendor/autoload.php';

/** @var Tarantool\Queue\Queue $queue */
$queue = require __DIR__.'/queue.php';

$task = $queue->put([
    'payload' => date('r'),
]);

printf("Added #%d: %s\n", $task->getId(), json_encode($task->getData()));
