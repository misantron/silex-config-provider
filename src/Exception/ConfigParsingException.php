<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Exception;

/**
 * Class ConfigurationParseException
 * @package Misantron\Silex\Provider\Exception
 */
class ConfigParsingException extends \RuntimeException
{
    public static function withReason(string $reason): self
    {
        return new self('Unable to parse config file: ' . $reason);
    }
}
