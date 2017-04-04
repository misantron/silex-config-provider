<?php

namespace Misantron\Silex\Provider\Adapter;


use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlConfigAdapter implements AdapterInterface
{
    /**
     * @param \SplFileInfo $file
     * @return array
     */
    public function load(\SplFileInfo $file): array
    {
        if ($file->getExtension() !== 'yml') {
            throw new \RuntimeException();
        }
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            throw new \RuntimeException('');
        }

        try {
            $config = Yaml::parse(file_get_contents($file->getRealPath()));
        } catch (ParseException $e) {
            throw new \RuntimeException('');
        }

        return $config;
    }
}