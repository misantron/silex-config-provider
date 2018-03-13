<?php

namespace Misantron\Silex\Provider\Tests\Adapter;


use Misantron\Silex\Provider\Adapter\YamlConfigAdapter;
use Misantron\Silex\Provider\Tests\AdapterTrait;
use PHPUnit\Framework\TestCase;

class YamlConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp()
    {
        $this->adapter = new YamlConfigAdapter();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to parse config file: Malformed inline YAML string: {bar} test at line 1 (near "foo: {bar} test").
     */
    public function testLoadInvalidConfigFile()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.yml');

        $this->adapter->load($file);
    }

    public function testLoad()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.yml');

        $config = $this->adapter->load($file);

        $this->assertEquals(['foo' => 'bar'], $config);
    }
}