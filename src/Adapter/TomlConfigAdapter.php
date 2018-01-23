<?php

namespace Misantron\Silex\Provider\Adapter;

use Misantron\Silex\Provider\ConfigAdapter;

/**
 * Class TomlConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class TomlConfigAdapter extends ConfigAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function parse(\SplFileInfo $file): array
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('\\Toml')) {
            throw new \RuntimeException('Toml parser component is not installed');
        }
        // @codeCoverageIgnoreEnd

        try {
            $config = \Toml::parseFile($file->getRealPath());
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to parse config file: ' . $e->getMessage());
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function configFileExtensions(): array
    {
        return ['toml'];
    }
}