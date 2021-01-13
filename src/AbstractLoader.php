<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider;

use Misantron\Silex\Provider\Exception\ConfigParsingException;
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

    /**
     * @return array
     *
     * @throws ConfigParsingException
     * @throws \AssertionError
     */
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
        return $this->file->getRealPath();
    }

    protected function getFileContents(): string
    {
        $file = $this->file->openFile();

        $contents = $file->fread($file->getSize());
        assert(\is_string($contents));

        return $contents;
    }
}
