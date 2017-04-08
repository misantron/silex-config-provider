<?php

namespace Misantron\Silex\Provider\Adapter;


class IniConfigAdapter implements ConfigAdapterInterface
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
        if ($file->getExtension() !== 'ini') {
            throw new \RuntimeException('Invalid config file type provided');
        }

        $config = parse_ini_file($file->getRealPath());
        // @codeCoverageIgnoreStart
        if ($config === false) {
            throw new \RuntimeException('Unable to parse config file');
        }
        // @codeCoverageIgnoreEnd

        return $config;
    }
}