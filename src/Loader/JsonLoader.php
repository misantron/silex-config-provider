<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Loader;

use Misantron\Silex\Provider\AbstractLoader;
use Misantron\Silex\Provider\Exception\ConfigParsingException;
use Webmozart\Assert\InvalidArgumentException;

/**
 * @package Misantron\Silex\Provider\Loader
 */
class JsonLoader extends AbstractLoader
{
    protected function parse(): array
    {
        try {
            $config = json_decode($this->getFileContents(), true, flags: JSON_THROW_ON_ERROR);
        } catch (InvalidArgumentException | \JsonException $exception) {
            throw ConfigParsingException::withReason($exception->getMessage());
        }

        return $config;
    }

    public function getSupportedExtensions(): array
    {
        return ['json'];
    }
}
