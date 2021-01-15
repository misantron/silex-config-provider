<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests\Loader;

use Misantron\Silex\Provider\Exception\ConfigParsingException;
use Misantron\Silex\Provider\Loader\XmlLoader;
use PHPUnit\Framework\TestCase;

class XmlLoaderTest extends TestCase
{
    public function testLoadInvalidConfigFile(): void
    {
        $this->expectException(ConfigParsingException::class);
        $this->expectExceptionMessage('Unable to parse config file: xmlParseEntityRef: no name');

        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.xml');

        (new XmlLoader($file))->load();
    }

    public function testLoad(): void
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.xml');

        $config = (new XmlLoader($file))->load();

        self::assertSame(['foo' => 'bar'], $config);
    }
}
