<?php

namespace Misantron\Silex\Provider\Adapter;


/**
 * Class TomlConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class TomlConfigAdapter implements ConfigAdapterInterface
{
    /**
     * @param \SplFileInfo $file
     * @return array
     */
    public function load(\SplFileInfo $file): array
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('Toml')) {
            throw new \RuntimeException('Toml parser component is not installed');
        }
        // @codeCoverageIgnoreEnd
        if (!$file->isReadable()) {
            throw new \RuntimeException('Config file is not readable');
        }
        if ($file->getExtension() !== 'toml') {
            throw new \RuntimeException('Invalid config file type provided');
        }
        try {
            $config = \Toml::parseFile($file->getRealPath());
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to parse config file: ' . $e->getMessage());
        }

        return $config;
    }
}