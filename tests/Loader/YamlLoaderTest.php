<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests\Loader;

use Misantron\Silex\Provider\Exception\ConfigParsingException;
use Misantron\Silex\Provider\Loader\YamlLoader;
use PHPUnit\Framework\TestCase;

final class YamlLoaderTest extends TestCase
{
    public function testLoadInvalidConfigFile(): void
    {
        $this->expectException(ConfigParsingException::class);
        $this->expectExceptionMessage(
            'Unable to parse config file: Unexpected token &quot;test&quot; at line 1 (near &quot;foo: {bar} test&quot;).',
        );

        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.yml');

        (new YamlLoader($file))->load();
    }

    public function testLoad(): void
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.yml');

        $config = (new YamlLoader($file))->load();

        $this->assertSame([
            'foo' => 'bar',
        ], $config);
    }
}
