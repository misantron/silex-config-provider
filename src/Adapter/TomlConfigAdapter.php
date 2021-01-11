<?php

namespace Misantron\Silex\Provider\Adapter;

use Misantron\Silex\Provider\ConfigAdapter;
use Misantron\Silex\Provider\Exception\ComponentNotInstalledException;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;

/**
 * Class TomlConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class TomlConfigAdapter extends ConfigAdapter
{
    /**
     * @param \SplFileInfo $file
     * @return array
     *
     * @throws ConfigurationParseException
     */
    protected function parse(\SplFileInfo $file): array
    {
        try {
            $config = \Toml::parseFile($file->getRealPath());
        } catch (\Throwable $e) {
            throw new ConfigurationParseException('Unable to parse config file: ' . $e->getMessage());
        }

        return json_decode(json_encode($config), true);
    }

    /**
     * @return array
     */
    protected function configFileExtensions(): array
    {
        return ['toml'];
    }

    /**
     * @throws ComponentNotInstalledException
     */
    protected function assertComponentInstalled()
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('\\Toml')) {
            throw new ComponentNotInstalledException('Toml parser component is not installed');
        }
        // @codeCoverageIgnoreEnd
    }
}
