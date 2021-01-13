<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider;

/**
 * Interface LoaderFactoryInterface
 * @package Misantron\Silex\Provider
 */
interface LoaderFactoryInterface
{
    public function create(string $path): LoaderInterface;
}
