<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests\Loader;

use Misantron\Silex\Provider\Exception\ConfigParsingException;
use Misantron\Silex\Provider\Loader\PhpLoader;
use PHPUnit\Framework\TestCase;

class PhpLoaderTest extends TestCase
{
    public function testLoadInvalidConfigFile(): void
    {
        $this->expectException(ConfigParsingException::class);
        $this->expectExceptionMessage('Unable to parse config file: config is not iterable');

        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.php');

        (new PhpLoader($file))->load();
    }

    public function testLoad(): void
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.php');

        $config = (new PhpLoader($file))->load();

        self::assertSame(['foo' => 'bar'], $config);
    }
}
