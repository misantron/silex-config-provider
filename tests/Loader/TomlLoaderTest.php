<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests\Loader;

use Composer\InstalledVersions;
use Misantron\Silex\Provider\Exception\ConfigParsingException;
use Misantron\Silex\Provider\Loader\TomlLoader;
use PHPUnit\Framework\TestCase;

class TomlLoaderTest extends TestCase
{
    public function testLoadWithoutParserInstalled(): void
    {
        $data = InstalledVersions::getRawData();
        InstalledVersions::reload([]);

        $file = new \SplFileInfo(__DIR__ . '/../resources/base.toml');

        try {
            (new TomlLoader($file))->load();
        } catch (\Throwable $e) {
            self::assertInstanceOf(\AssertionError::class, $e);
            self::assertSame('Toml parser library is not installed', $e->getMessage());

            // restore versions data after testing
            InstalledVersions::reload($data);
            return;
        }

        self::fail('No exception was thrown');
    }

    public function testLoadInvalidConfigFile(): void
    {
        $this->expectException(ConfigParsingException::class);
        $this->expectExceptionMessageMatches(
            '/^Unable to parse config file: Syntax error: unexpected token "T_EOS" at line 3 with value ""/'
        );

        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.toml');

        (new TomlLoader($file))->load();
    }

    public function testLoad(): void
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.toml');

        $config = (new TomlLoader($file))->load();

        self::assertSame(['test' => ['foo' => 'bar']], $config);
    }
}
