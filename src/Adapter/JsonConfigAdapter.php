<?php

namespace Misantron\Silex\Provider\Adapter;

use Misantron\Silex\Provider\ConfigAdapter;

/**
 * Class JsonConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class JsonConfigAdapter extends ConfigAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function parse(\SplFileInfo $file): array
    {
        $config = json_decode(file_get_contents($file->getRealPath()), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Unable to parse JSON file: ' . json_last_error_msg());
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function configFileExtensions(): array
    {
        return ['json'];
    }
}