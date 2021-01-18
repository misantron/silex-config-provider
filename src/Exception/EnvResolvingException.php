<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Exception;

/**
 * Class EnvResolvingException
 * @package Misantron\Silex\Provider\Exception
 */
class EnvResolvingException extends \RuntimeException
{
    public static function undefinedVariable(string $name): self
    {
        return new self(
            sprintf('Environment variable %s not found', $name)
        );
    }

    public static function invalidJson(string $name, string $message): self
    {
        return new self(
            sprintf('Invalid JSON value %s: %s', $name, $message)
        );
    }

    public static function unsupportedPrefix(string $name, string $prefix): self
    {
        return new self(
            sprintf('Unsupported prefix for %s provided: %s', $name, $prefix)
        );
    }
}
