<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Exception;

/**
 * @package Misantron\Silex\Provider\Exception
 */
final class InvalidConfigException extends \RuntimeException
{
    public static function unsupportedFileType(string $ext): self
    {
        return new self(
            sprintf('Unsupported config file type provided: %s', $ext),
        );
    }

    public static function notAFile(): self
    {
        return new self('Config is not a file');
    }

    public static function notReadable(): self
    {
        return new self('Config file is not readable');
    }

    public static function emptyContent(): self
    {
        return new self('No configuration data provided');
    }
}
