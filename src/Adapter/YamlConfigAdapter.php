<?php

namespace Misantron\Silex\Provider\Adapter;


use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlConfigAdapter implements ConfigAdapterInterface
{
    /**
     * @param \SplFileInfo $file
     * @return array
     */
    public function load(\SplFileInfo $file): array
    {
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            throw new \RuntimeException('Symfony yaml component is not installed');
        }
        if (!$file->isReadable()) {
            throw new \RuntimeException('Config file is not readable');
        }
        if ($file->getExtension() !== 'yml' && $file->getExtension() !== 'yaml') {
            throw new \RuntimeException('Invalid config file type provided');
        }

        $contents = file_get_contents($file->getRealPath());
        if ($contents === false) {
            throw new \RuntimeException('Unable to read config file');
        }

        try {
            $config = Yaml::parse($contents);
        } catch (ParseException $e) {
            throw new \RuntimeException('Unable to parse config file');
        }

        return $config;
    }
}