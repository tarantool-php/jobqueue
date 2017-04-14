<?php

namespace Tarantool\JobQueue\Exception;

class BadPayloadException extends \RuntimeException
{
    private $payload;

    public function __construct($payload, $message = '', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message ?: 'Bad payload.', $code, $previous);

        $this->payload = $payload;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public static function unexpectedType($payload, string $expectedType, string $class): self
    {
        $message = sprintf('%s expects payload to be %s, %s given.',
            $class,
            $expectedType,
            is_object($payload) ? get_class($payload) : gettype($payload)
        );

        return new self($payload, $message);
    }

    public static function missingOrEmptyKeyValue($payload, string $key, string $type, string $class): self
    {
        $message = "$class requires payload to have a \"$key\" key with a non-empty $type value.";

        return new self($payload, $message);
    }
}
