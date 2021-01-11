<?php

namespace Misantron\Silex\Provider\Tests\Unit\Adapter;

use Misantron\Silex\Provider\Adapter\TomlConfigAdapter;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;
use Misantron\Silex\Provider\Tests\TestCase;
use Misantron\Silex\Provider\Tests\Unit\AdapterTrait;

class TomlConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp(): void
    {
        $this->adapter = new TomlConfigAdapter();
    }

    public function testLoadInvalidConfigFile(): void
    {
        $this->expectException(ConfigurationParseException::class);
        $this->expectExceptionMessage('Unable to parse config file: Syntax error found on TOML document. Missing closing string delimiter.');

        $file = new \SplFileInfo(__DIR__ . '/../../resources/invalid.toml');

        $this->adapter->load($file);
    }

    public function testLoad(): void
    {
        $file = new \SplFileInfo(__DIR__ . '/../../resources/base.toml');

        $config = $this->adapter->load($file);

        self::assertSame(['test' => ['foo' => 'bar']], $config);
    }
}
