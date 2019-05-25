<?php

namespace Misantron\Silex\Provider\Tests\Unit\Adapter;

use Misantron\Silex\Provider\Adapter\YamlConfigAdapter;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;
use Misantron\Silex\Provider\Tests\Unit\AdapterTrait;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

class YamlConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp()
    {
        $this->adapter = new YamlConfigAdapter();
    }

    public function testLoadInvalidConfigFile()
    {
        $this->expectException(ConfigurationParseException::class);
        $this->expectExceptionMessage('Unable to parse config file: Malformed inline YAML string: {bar} test at line 1 (near "foo: {bar} test").');

        $file = new SplFileInfo(__DIR__ . '/../../resources/invalid.yml');

        $this->adapter->load($file);
    }

    public function testLoad()
    {
        $file = new SplFileInfo(__DIR__ . '/../../resources/base.yml');

        $config = $this->adapter->load($file);

        $this->assertEquals(['foo' => 'bar'], $config);
    }
}