<?php

namespace Misantron\Silex\Provider\Adapter;

use Misantron\Silex\Provider\ConfigAdapter;

/**
 * Class IniConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class IniConfigAdapter extends ConfigAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function parse(\SplFileInfo $file): array
    {
        $config = parse_ini_file($file->getRealPath());

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function configFileExtensions(): array
    {
        return ['ini'];
    }
}