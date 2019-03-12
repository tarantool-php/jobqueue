<?php

namespace Tarantool\JobQueue;

abstract class JobOptions
{
    public const PAYLOAD = 'payload';
    public const PAYLOAD_SERVICE_ID = 'service';
    public const PAYLOAD_SERVICE_METHOD = 'method';
    public const PAYLOAD_SERVICE_ARGS = 'args';
    public const RETRY_LIMIT = 'retry_limit';
    public const RETRY_ATTEMPT = 'retry_attempt';
    public const RETRY_STRATEGY = 'retry_strategy';
    public const RECURRENCE = 'recurrence';
}
