<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Loader;

use Misantron\Silex\Provider\AbstractLoader;
use Misantron\Silex\Provider\Exception\ConfigParsingException;

/**
 * Class JsonLoader
 * @package Misantron\Silex\Provider\Loader
 */
class JsonLoader extends AbstractLoader
{
    protected function parse(): array
    {
        try {
            $config = json_decode($this->getFileContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\AssertionError | \JsonException $e) {
            throw ConfigParsingException::withReason($e->getMessage());
        }

        return $config;
    }

    public function getSupportedExtensions(): array
    {
        return ['json'];
    }
}
