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
     * @param \SplFileInfo $file
     * @return array
     */
    protected function parse(\SplFileInfo $file): array
    {
        $this->assertLibraryInstalled();

        try {
            $config = (new Parser())->parse(file_get_contents($file->getRealPath()));
        } catch (ParseException $e) {
            throw new \RuntimeException('Unable to parse config file: ' . $e->getMessage());
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
     * @throws \RuntimeException
     */
    private function assertLibraryInstalled()
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            throw new \RuntimeException('Yaml parser component is not installed');
        }
        // @codeCoverageIgnoreEnd
    }
}