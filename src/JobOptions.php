<?php

namespace Tarantool\JobQueue;

abstract class JobOptions
{
    const PAYLOAD = 'payload';
    const PAYLOAD_SERVICE = 'service';
    const PAYLOAD_ARGS = 'args';
    const RETRY_LIMIT = 'retry_limit';
    const RETRY_ATTEMPT = 'retry_attempt';
    const RETRY_STRATEGY = 'retry_strategy';
    const RECURRENCE = 'recurrence';
}
