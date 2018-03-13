<?php

namespace Misantron\Silex\Provider\Tests\Adapter;


use Misantron\Silex\Provider\Adapter\IniConfigAdapter;
use Misantron\Silex\Provider\Tests\AdapterTrait;
use PHPUnit\Framework\TestCase;

class IniConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp()
    {
        $this->adapter = new IniConfigAdapter();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to parse config: invalid format
     */
    public function testLoadInvalidConfigFile()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.ini');

        $this->adapter->load($file);
    }

    public function testLoad()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.ini');

        $config = $this->adapter->load($file);

        $this->assertEquals(['foo' => 'bar'], $config);
    }
}