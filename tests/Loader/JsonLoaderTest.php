<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests\Loader;

use Misantron\Silex\Provider\Exception\ConfigParsingException;
use Misantron\Silex\Provider\Loader\JsonLoader;
use PHPUnit\Framework\TestCase;

class JsonLoaderTest extends TestCase
{
    public function testLoadInvalidConfigFile(): void
    {
        $this->expectException(ConfigParsingException::class);
        $this->expectExceptionMessage('Unable to parse config file: Syntax error');

        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.json');

        (new JsonLoader($file))->load();
    }

    public function testLoad(): void
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.json');

        $config = (new JsonLoader($file))->load();

        self::assertSame(['foo' => 'bar'], $config);
    }
}
