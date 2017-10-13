<?php

namespace Tarantool\JobQueue\Listener\Event;

trait HasError
{
    private $error;

    private function setError(\Throwable $error)
    {
        $this->error = $error;
    }

    public function getError(): \Throwable
    {
        return $this->error;
    }
}
