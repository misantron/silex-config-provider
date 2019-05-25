<?php

namespace Misantron\Silex\Provider\Adapter;

use Misantron\Silex\Provider\ConfigAdapter;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;
use SplFileInfo;

/**
 * Class IniConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class IniConfigAdapter extends ConfigAdapter
{
    /**
     * @param SplFileInfo $file
     * @return array
     *
     * @throws ConfigurationParseException
     */
    protected function parse(SplFileInfo $file): array
    {
        $config = @parse_ini_file($file->getRealPath());
        if ($config === false) {
            throw new ConfigurationParseException('Unable to parse configuration: invalid format');
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