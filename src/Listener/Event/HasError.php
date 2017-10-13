<?php

namespace Tarantool\JobQueue\Listener;

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
