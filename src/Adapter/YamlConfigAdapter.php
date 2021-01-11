<?php

namespace Misantron\Silex\Provider\Adapter;

use Misantron\Silex\Provider\ConfigAdapter;
use Misantron\Silex\Provider\Exception\ComponentNotInstalledException;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * Class YamlConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class YamlConfigAdapter extends ConfigAdapter
{
    /**
     * @param \SplFileInfo $file
     * @return array
     *
     * @throws ConfigurationParseException
     */
    protected function parse(\SplFileInfo $file): array
    {
        try {
            $config = (new Parser())->parseFile($file->getRealPath());
        } catch (ParseException $e) {
            throw new ConfigurationParseException('Unable to parse config file: ' . $e->getMessage());
        }

        return $config;
    }

    /**
     * @return array
     */
    protected function configFileExtensions(): array
    {
        return ['yml', 'yaml'];
    }

    /**
     * @throws ComponentNotInstalledException
     */
    protected function assertComponentInstalled()
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            throw new ComponentNotInstalledException('Yaml parser component is not installed');
        }
        // @codeCoverageIgnoreEnd
    }
}
