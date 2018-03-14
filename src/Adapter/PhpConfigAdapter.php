<?php

namespace Misantron\Silex\Provider\Adapter;

use Misantron\Silex\Provider\ConfigAdapter;

/**
 * Class PhpConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class PhpConfigAdapter extends ConfigAdapter
{
    /**
     * @param \SplFileInfo $file
     * @return array
     */
    protected function parse(\SplFileInfo $file): array
    {
        $config = require_once $file->getRealPath();

        if (!is_array($config)) {
            throw new \RuntimeException('Invalid config file');
        }

        return $config;
    }

    /**
     * @return array
     */
    protected function configFileExtensions(): array
    {
        return ['php'];
    }
}