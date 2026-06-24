<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider;

use Misantron\Silex\Provider\Exception\InvalidConfigException;

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
            throw InvalidConfigException::notAFile();
        }
        if (!$file->isReadable()) {
            throw InvalidConfigException::notReadable();
        }

        return $this->createLoaderByFileExtension($file);
    }

    private function createLoaderByFileExtension(\SplFileInfo $file): LoaderInterface
    {
        return match ($file->getExtension()) {
            'json' => new Loader\JsonLoader($file),
            'php' => new Loader\PhpLoader($file),
            'yml', 'yaml' => new Loader\YamlLoader($file),
            default => throw InvalidConfigException::unsupportedFileType($file->getExtension()),
        };
    }
}
