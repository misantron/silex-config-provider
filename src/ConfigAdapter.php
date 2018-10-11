<?php

namespace Misantron\Silex\Provider;

use Misantron\Silex\Provider\Exception\ComponentNotInstalledException;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;
use Misantron\Silex\Provider\Exception\InvalidConfigurationException;

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
        $this->assertComponentInstalled();

        return $this->parse($file);
    }

    /**
     * @param \SplFileInfo $file
     * @return array
     *
     * @throws ConfigurationParseException
     */
    abstract protected function parse(\SplFileInfo $file): array;

    /**
     * @return array
     */
    abstract protected function configFileExtensions(): array;

    /**
     * @throws ComponentNotInstalledException
     */
    protected function assertComponentInstalled()
    {

    }

    /**
     * @param \SplFileInfo $file
     *
     * @throws InvalidConfigurationException
     */
    private function validateFile(\SplFileInfo $file)
    {
        if (!$file->isReadable()) {
            throw new InvalidConfigurationException('Configuration file is not readable');
        }
        $validExtensions = $this->configFileExtensions();
        if (!in_array($file->getExtension(), $validExtensions, true)) {
            throw new InvalidConfigurationException('Invalid configuration file type provided');
        }
    }
}