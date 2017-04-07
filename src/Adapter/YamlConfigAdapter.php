<?php

namespace Misantron\Silex\Provider\Adapter;


use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlConfigAdapter implements ConfigAdapterInterface
{
    /**
     * @param \SplFileInfo $file
     * @return array
     */
    public function load(\SplFileInfo $file): array
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            throw new \RuntimeException('Symfony yaml component is not installed');
        }
        // @codeCoverageIgnoreEnd
        if (!$file->isReadable()) {
            throw new \RuntimeException('Config file is not readable');
        }
        if ($file->getExtension() !== 'yml' && $file->getExtension() !== 'yaml') {
            throw new \RuntimeException('Invalid config file type provided');
        }

        try {
            $config = (new Parser())->parse(file_get_contents($file->getRealPath()));
        } catch (ParseException $e) {
            throw new \RuntimeException('Unable to parse config file: ' . $e->getMessage());
        }

        return $config;
    }
}