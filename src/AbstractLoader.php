<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider;

use Misantron\Silex\Provider\Exception\InvalidConfigException;

/**
 * Class AbstractLoader
 * @package Misantron\Silex\Provider
 */
abstract class AbstractLoader implements LoaderInterface
{
    private \SplFileInfo $file;

    public function __construct(\SplFileInfo $file)
    {
        $this->file = $file;
    }

    abstract protected function parse(): array;

    public function load(): array
    {
        if (!\in_array($this->file->getExtension(), $this->getSupportedExtensions(), true)) {
            throw InvalidConfigException::unsupportedFileType($this->file->getExtension());
        }

        return $this->parse();
    }

    protected function getFilePath(): string
    {
        $path = $this->file->getRealPath();
        assert(
            \is_string($path),
            'File path is invalid'
        );

        return $path;
    }

    protected function getFileContents(): string
    {
        $file = $this->file->openFile();

        $contents = $file->fread($file->getSize());
        assert(
            \is_string($contents),
            'File content reading error'
        );

        return $contents;
    }
}
