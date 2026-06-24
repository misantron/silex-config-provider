<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Exception;

/**
 * @package Misantron\Silex\Provider\Exception
 */
final class ConfigParsingException extends \RuntimeException
{
    public static function withReason(string $reason): self
    {
        return new self(
            sprintf('Unable to parse config file: %s', $reason),
        );
    }
}
