<?php

namespace Misantron\Silex\Provider\Adapter;


use Misantron\Silex\Provider\ConfigAdapter;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * Class YamlConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class YamlConfigAdapter extends ConfigAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function parse(\SplFileInfo $file): array
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            throw new \RuntimeException('Yaml parser component is not installed');
        }
        // @codeCoverageIgnoreEnd

        try {
            $config = (new Parser())->parse(file_get_contents($file->getRealPath()));
        } catch (ParseException $e) {
            throw new \RuntimeException('Unable to parse config file: ' . $e->getMessage());
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function configFileExtensions(): array
    {
        return ['yml', 'yaml'];
    }
}