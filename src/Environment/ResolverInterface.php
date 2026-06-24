<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Environment;

/**
 * @package Misantron\Silex\Provider\Environment
 */
interface ResolverInterface
{
    public function resolve(string $value): mixed;
}
