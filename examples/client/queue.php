<?php

$factory = new Tarantool\JobQueue\DefaultConfigFactory();

return $factory->createQueue('foobar', $factory->createClient('tcp://127.0.0.1:3301'));
