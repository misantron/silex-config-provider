<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Loader;

use Misantron\Silex\Provider\AbstractLoader;
use Misantron\Silex\Provider\Exception\ConfigParsingException;

/**
 * Class PhpLoader
 * @package Misantron\Silex\Provider\Loader
 */
class PhpLoader extends AbstractLoader
{
    protected function parse(): array
    {
        $config = require $this->getFilePath();
        if (!\is_iterable($config)) {
            throw ConfigParsingException::withReason('config is not iterable');
        }
        if ($config instanceof \Traversable) {
            $config = iterator_to_array($config);
        }

        return $config;
    }

    public function getSupportedExtensions(): array
    {
        return ['php'];
    }
}
