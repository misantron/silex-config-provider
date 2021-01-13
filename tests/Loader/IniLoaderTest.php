<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests\Loader;

use Misantron\Silex\Provider\Exception\ConfigParsingException;
use Misantron\Silex\Provider\Exception\InvalidConfigException;
use Misantron\Silex\Provider\Loader\IniLoader;
use PHPUnit\Framework\TestCase;

class IniLoaderTest extends TestCase
{
    public function testLoadInvalidConfigFile(): void
    {
        $this->expectException(ConfigParsingException::class);
        $this->expectExceptionMessage('Unable to parse config file: invalid format');

        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.ini');

        (new IniLoader($file))->load();
    }

    public function testLoadInvalidTypeConfigFile(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Unsupported config file type provided: json');

        $file = new \SplFileInfo(__DIR__ . '/../resources/base.json');

        (new IniLoader($file))->load();
    }

    public function testLoad(): void
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.ini');

        $config = (new IniLoader($file))->load();

        self::assertSame(['foo' => 'bar'], $config);
    }
}
