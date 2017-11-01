<?php

namespace Tarantool\JobQueue\Executor\CallbackResolver;

use Psr\Container\ContainerInterface as Container;
use Tarantool\JobQueue\Exception\BadPayloadException;
use Tarantool\JobQueue\JobOptions;

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
        if (empty($payload[JobOptions::PAYLOAD_SERVICE])) {
            throw BadPayloadException::missingOrEmptyKeyValue($payload, JobOptions::PAYLOAD_SERVICE, 'string', __CLASS__);
        }

        $id = sprintf($this->idFormat, $payload[JobOptions::PAYLOAD_SERVICE]);

        return $this->container->get($id);
    }
}
