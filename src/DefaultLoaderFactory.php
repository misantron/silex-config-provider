<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider;

use Misantron\Silex\Provider\Exception\InvalidConfigException;
use Misantron\Silex\Provider\Loader;

/**
 * Class DefaultLoaderFactory
 * @package Misantron\Silex\Provider
 */
final class DefaultLoaderFactory implements LoaderFactoryInterface
{
    public function create(string $path): LoaderInterface
    {
        $file = new \SplFileInfo($path);

        if (!$file->isFile()) {
            throw InvalidConfigException::notFile();
        }
        if (!$file->isReadable()) {
            throw InvalidConfigException::notReadableFile();
        }

        return $this->createLoaderByFileExtension($file);
    }

    private function createLoaderByFileExtension(\SplFileInfo $file): LoaderInterface
    {
        switch ($file->getExtension()) {
            case 'ini':
                return new Loader\IniLoader($file);
            case 'json':
                return new Loader\JsonLoader($file);
            case 'php':
                return new Loader\PhpLoader($file);
            case 'xml':
                return new Loader\XmlLoader($file);
            case 'yml':
            case 'yaml':
                return new Loader\YamlLoader($file);
            default:
                throw InvalidConfigException::unsupportedFileType($file->getExtension());
        }
    }
}
