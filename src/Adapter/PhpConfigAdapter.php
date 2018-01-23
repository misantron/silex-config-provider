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
     * {@inheritdoc}
     */
    protected function parse(\SplFileInfo $file): array
    {
        $config = require $file->getRealPath();

        if (!is_array($config)) {
            throw new \RuntimeException('Invalid config file');
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function configFileExtensions(): array
    {
        return ['php'];
    }
}