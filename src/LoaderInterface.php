<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider;

/**
 * Interface LoaderInterface
 * @package Misantron\Silex\Provider
 */
interface LoaderInterface
{
    public function load(): array;

    public function getSupportedExtensions(): array;
}
