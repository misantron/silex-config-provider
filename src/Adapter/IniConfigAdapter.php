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
        $config = @parse_ini_file($file->getRealPath());
        if ($config === false) {
            throw new \RuntimeException('Unable to parse config: invalid format');
        }

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