<?php

namespace Tarantool\JobQueue\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Tarantool\JobQueue\Console\Command\ClearCommand;
use Tarantool\JobQueue\Console\Command\EnqueueCommand;
use Tarantool\JobQueue\Console\Command\RunCommand;
use Tarantool\JobQueue\Console\Command\StatsCommand;

class Application extends BaseApplication
{
    const VERSION = '0.x-DEV';

    public function __construct()
    {
        parent::__construct('Tarantool JobQueue', self::VERSION);

        $this->add(new ClearCommand());
        $this->add(new EnqueueCommand());
        $this->add(new RunCommand());
        $this->add(new StatsCommand());
    }
}
