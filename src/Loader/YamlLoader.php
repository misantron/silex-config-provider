<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Loader;

use Composer\InstalledVersions;
use Misantron\Silex\Provider\AbstractLoader;
use Misantron\Silex\Provider\Exception\ConfigParsingException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Webmozart\Assert\Assert;

/**
 * @package Misantron\Silex\Provider\Loader
 */
class YamlLoader extends AbstractLoader
{
    protected function parse(): array
    {
        // @codeCoverageIgnoreStart
        Assert::true(
            InstalledVersions::isInstalled('symfony/yaml'),
            'Yaml parser library is not installed',
        );
        // @codeCoverageIgnoreEnd

        try {
            $parser = new Parser();
            $config = $parser->parse($this->getFileContents());
        } catch (ParseException $exception) {
            throw ConfigParsingException::withReason(
                htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'),
            );
        }

        return $config;
    }

    public function getSupportedExtensions(): array
    {
        return ['yml', 'yaml'];
    }
}
