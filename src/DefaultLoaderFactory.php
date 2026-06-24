<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider;

use Misantron\Silex\Provider\Loader\JsonLoader;
use Misantron\Silex\Provider\Loader\PhpLoader;
use Misantron\Silex\Provider\Loader\YamlLoader;
use Misantron\Silex\Provider\Exception\InvalidConfigException;

/**
 * @package Misantron\Silex\Provider
 */
final class DefaultLoaderFactory implements LoaderFactoryInterface
{
    public function create(string $path): LoaderInterface
    {
        $file = new \SplFileInfo($path);

        if (! $file->isFile()) {
            throw InvalidConfigException::notAFile();
        }

        if (! $file->isReadable()) {
            throw InvalidConfigException::notReadable();
        }

        return $this->createLoaderByFileExtension($file);
    }

    private function createLoaderByFileExtension(\SplFileInfo $file): LoaderInterface
    {
        return match ($file->getExtension()) {
            'json' => new JsonLoader($file),
            'php' => new PhpLoader($file),
            'yml', 'yaml' => new YamlLoader($file),
            default => throw InvalidConfigException::unsupportedFileType(
                htmlspecialchars($file->getExtension(), ENT_QUOTES, 'UTF-8'),
            ),
        };
    }
}
