<?php

namespace Tarantool\JobQueue\Executor\CallbackResolver;

use Psr\Container\ContainerInterface as Container;
use Tarantool\JobQueue\Exception\BadPayloadException;
use Tarantool\JobQueue\JobOptions;

class ContainerCallbackResolver implements CallbackResolver
{
    private $container;
    private $idPrefix;

    public function __construct(Container $container, string $idPrefix = '')
    {
        $this->container = $container;
        $this->idPrefix = $idPrefix;
    }

    public function resolve($payload): callable
    {
        if (!empty($payload[JobOptions::PAYLOAD_SERVICE])) {
            return $this->container->get($this->idPrefix.$payload[JobOptions::PAYLOAD_SERVICE]);
        }

        throw BadPayloadException::missingOrEmptyKeyValue($payload, JobOptions::PAYLOAD_SERVICE, 'string', __CLASS__);
    }
}
