<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Exception;

/**
 * Class ComponentNotInstalledException
 * @package Misantron\Silex\Provider\Exception
 */
class ComponentNotInstalledException extends \RuntimeException
{
    public static function create(string $name): self
    {
        return new self($name . ' component is not installed');
    }
}
