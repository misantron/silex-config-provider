<?php

namespace Misantron\Silex\Provider\Adapter;


class PhpConfigAdapter implements ConfigAdapterInterface
{
    /**
     * @param \SplFileInfo $file
     * @return array
     */
    public function load(\SplFileInfo $file): array
    {
        if (!$file->isReadable()) {
            throw new \RuntimeException('Config file is not readable');
        }
        if ($file->getExtension() !== 'php') {
            throw new \RuntimeException('Invalid config file type provided');
        }

        $config = require $file->getRealPath();
        if (!is_array($config)) {
            throw new \RuntimeException('Invalid config file');
        }

        return $config;
    }
}