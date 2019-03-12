<?php

namespace Tarantool\JobQueue\Executor\CallbackResolver;

use Psr\Container\ContainerInterface as Container;
use Tarantool\JobQueue\Exception\BadPayloadException;
use Tarantool\JobQueue\JobBuilder\JobOptions;

class Psr11ContainerCallbackResolver implements CallbackResolver
{
    private $container;
    private $idFormat;

    public function __construct(Container $container, string $idFormat = '%s')
    {
        $this->container = $container;
        $this->idFormat = $idFormat;
    }

    public function resolve($payload): callable
    {
        if (empty($payload[JobOptions::PAYLOAD_SERVICE_ID])) {
            throw BadPayloadException::missingOrEmptyKeyValue($payload, JobOptions::PAYLOAD_SERVICE_ID, 'string', __CLASS__);
        }

        $id = sprintf($this->idFormat, $payload[JobOptions::PAYLOAD_SERVICE_ID]);

        return empty($payload[JobOptions::PAYLOAD_SERVICE_METHOD])
            ? $this->container->get($id)
            : [$this->container->get($id), $payload[JobOptions::PAYLOAD_SERVICE_METHOD]];
    }
}
