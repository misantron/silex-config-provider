<?php

namespace Misantron\Silex\Provider\Adapter;

use Misantron\Silex\Provider\ConfigAdapter;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;

/**
 * Class PhpConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class PhpConfigAdapter extends ConfigAdapter
{
    /**
     * @param \SplFileInfo $file
     * @return array
     *
     * @throws ConfigurationParseException
     */
    protected function parse(\SplFileInfo $file): array
    {
        $config = require $file->getRealPath();

        if (!is_array($config)) {
            throw new ConfigurationParseException('Invalid configuration file');
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
