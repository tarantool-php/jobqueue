<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Psr\Log\LoggerInterface as Logger;
use Xtreamwayz\Pimple\Container;

$container = new Container([
    'job.greet.yell' => false,
    'logger.file' => __DIR__.'/worker.log',
]);

$container['job.greet'] = function ($c) {
    return function (string $name, Logger $logger) use ($c) {
        $text = $name ? 'Hello '.$name : 'Hello';

        if ($c['job.greet.yell']) {
            $text = strtoupper($text);
        }

        $logger->info($text);
    };
};

$container['logger'] = function ($c) {
    return new MonologLogger('worker', [new StreamHandler($c['logger.file'])]);
};

$container['autowiring.job_args'] = function ($c) {
    return [
        'logger' => $c['logger'],
    ];
};

return $container;
