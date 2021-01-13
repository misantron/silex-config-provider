<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Loader;

use Composer\InstalledVersions;
use Misantron\Silex\Provider\AbstractLoader;
use Misantron\Silex\Provider\Exception\ConfigParsingException;
use Yosymfony\ParserUtils\SyntaxErrorException;
use Yosymfony\Toml\Lexer;
use Yosymfony\Toml\Parser;

/**
 * Class TomlLoader
 * @package Misantron\Silex\Provider\Loader
 */
class TomlLoader extends AbstractLoader
{
    protected function parse(): array
    {
        // @codeCoverageIgnoreStart
        assert(InstalledVersions::isInstalled('yosymfony/toml'));
        // @codeCoverageIgnoreEnd

        try {
            $parser = new Parser(new Lexer());
            $config = $parser->parse($this->getFileContents());
        } catch (SyntaxErrorException $e) {
            throw ConfigParsingException::withReason($e->getMessage());
        }

        return $config;
    }

    public function getSupportedExtensions(): array
    {
        return ['toml'];
    }
}
