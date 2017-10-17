<?php

namespace Tarantool\JobQueue\Listener\Event;

trait HasError
{
    private $error;

    private function setError(\Throwable $error): void
    {
        $this->error = $error;
    }

    public function getError(): \Throwable
    {
        return $this->error;
    }
}
