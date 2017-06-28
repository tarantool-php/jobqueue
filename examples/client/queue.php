<?php

return (new Tarantool\JobQueue\DefaultConfigFactory())
    ->setConnectionUri('tcp://127.0.0.1:3301')
    ->setQueueName('foobar')
    ->createQueue();
