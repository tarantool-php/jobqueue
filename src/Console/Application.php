<?php

namespace Tarantool\JobQueue\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Tarantool\JobQueue\Console\Command\KickCommand;
use Tarantool\JobQueue\Console\Command\PutCommand;
use Tarantool\JobQueue\Console\Command\RunCommand;
use Tarantool\JobQueue\Console\Command\StatsCommand;
use Tarantool\JobQueue\Console\Command\TruncateCommand;

class Application extends BaseApplication
{
    const VERSION = '0.x-DEV';

    public function __construct()
    {
        parent::__construct('Tarantool JobQueue', self::VERSION);

        $this->add(new KickCommand());
        $this->add(new PutCommand());
        $this->add(new RunCommand());
        $this->add(new StatsCommand());
        $this->add(new TruncateCommand());
    }
}
