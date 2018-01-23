<?php

namespace Misantron\Silex\Provider;

/**
 * Class ConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
abstract class ConfigAdapter
{
    /**
     * @param \SplFileInfo $file
     * @return array
     */
    public function load(\SplFileInfo $file): array
    {
        $this->validateFile($file);

        return $this->parse($file);
    }

    /**
     * @param \SplFileInfo $file
     * @return array
     */
    abstract protected function parse(\SplFileInfo $file): array;

    /**
     * @return array
     */
    abstract protected function configFileExtensions(): array;

    /**
     * @param \SplFileInfo $file
     * @throws \RuntimeException
     */
    private function validateFile(\SplFileInfo $file)
    {
        if (!$file->isReadable()) {
            throw new \RuntimeException('Config file is not readable');
        }
        $validExtensions = $this->configFileExtensions();
        if (!in_array($file->getExtension(), $validExtensions, true)) {
            throw new \RuntimeException('Invalid config file type provided');
        }
    }
}