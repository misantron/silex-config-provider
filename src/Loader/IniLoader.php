<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Loader;

use Misantron\Silex\Provider\AbstractLoader;
use Misantron\Silex\Provider\Exception\ConfigParsingException;

/**
 * Class IniLoader
 * @package Misantron\Silex\Provider\Loader
 */
class IniLoader extends AbstractLoader
{
    protected function parse(): array
    {
        $config = @parse_ini_file($this->getFilePath(), true);
        if ($config === false) {
            throw ConfigParsingException::withReason('invalid format');
        }

        return $config;
    }

    public function getSupportedExtensions(): array
    {
        return ['ini'];
    }
}
