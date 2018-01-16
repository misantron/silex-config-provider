<?php

namespace Misantron\Silex\Provider\Adapter;


/**
 * Class JsonConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class JsonConfigAdapter implements ConfigAdapterInterface
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
        if ($file->getExtension() !== 'json') {
            throw new \RuntimeException('Invalid config file type provided');
        }

        $config = json_decode(file_get_contents($file->getRealPath()), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Unable to parse JSON file: ' . json_last_error_msg());
        }

        return $config;
    }
}