<?php

require __DIR__.'/../../vendor/autoload.php';

/** @var Tarantool\Queue\Queue $queue */
$queue = require __DIR__.'/queue.php';

$task = $queue->put([
    'payload' => 'sleep 5; date >> '.__DIR__.'/jobqueue_process.log',
]);

printf("Added #%d: %s\n", $task->getId(), json_encode($task->getData()));
