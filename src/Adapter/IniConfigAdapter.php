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
     * @param \SplFileInfo $file
     * @return array
     */
    protected function parse(\SplFileInfo $file): array
    {
        $config = parse_ini_file($file->getRealPath());

        return $config;
    }

    /**
     * @return array
     */
    protected function configFileExtensions(): array
    {
        return ['ini'];
    }
}