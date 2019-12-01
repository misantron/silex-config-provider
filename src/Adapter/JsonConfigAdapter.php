<?php

namespace Misantron\Silex\Provider\Adapter;

use Misantron\Silex\Provider\ConfigAdapter;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;

/**
 * Class JsonConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class JsonConfigAdapter extends ConfigAdapter
{
    /**
     * @param \SplFileInfo $file
     * @return array
     *
     * @throws ConfigurationParseException
     */
    protected function parse(\SplFileInfo $file): array
    {
        $config = json_decode(file_get_contents($file->getRealPath()), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ConfigurationParseException('Unable to parse json file: ' . json_last_error_msg());
        }

        return $config;
    }

    /**
     * @return array
     */
    protected function configFileExtensions(): array
    {
        return ['json'];
    }
}