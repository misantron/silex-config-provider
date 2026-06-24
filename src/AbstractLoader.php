<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider;

use Misantron\Silex\Provider\Exception\InvalidConfigException;
use Webmozart\Assert\Assert;

/**
 * @package Misantron\Silex\Provider
 */
abstract class AbstractLoader implements LoaderInterface
{
    public function __construct(
        private readonly \SplFileInfo $file,
    ) {}

    abstract protected function parse(): array;

    public function load(): array
    {
        if (! \in_array($this->file->getExtension(), $this->getSupportedExtensions(), true)) {
            throw InvalidConfigException::unsupportedFileType($this->file->getExtension());
        }

        return $this->parse();
    }

    protected function getFilePath(): string
    {
        $path = $this->file->getRealPath();
        Assert::string(
            $path,
            'File path is invalid',
        );

        return $path;
    }

    protected function getFileContents(): string
    {
        $file = $this->file->openFile();

        $contents = $file->fread($file->getSize());
        Assert::string(
            $contents,
            'File content reading error',
        );

        return $contents;
    }
}
