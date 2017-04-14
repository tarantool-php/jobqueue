<?php

namespace Tarantool\JobQueue\Runner;

interface Runner
{
    public function run(int $idleTimeout = 1);
}
