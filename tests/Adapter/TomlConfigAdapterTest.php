<?php

namespace Misantron\Silex\Provider\Tests\Adapter;


use Misantron\Silex\Provider\Adapter\TomlConfigAdapter;
use Misantron\Silex\Provider\Tests\AdapterTrait;
use PHPUnit\Framework\TestCase;

class TomlConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp()
    {
        $this->adapter = new TomlConfigAdapter();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to parse config file: Syntax error found on TOML document. Missing closing string delimiter.
     */
    public function testLoadInvalidConfigFile()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.toml');

        $this->adapter->load($file);
    }

    public function testLoad()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.toml');

        $config = $this->adapter->load($file);

        $this->assertEquals(['test' => ['foo' => 'bar']], $config);
    }
}