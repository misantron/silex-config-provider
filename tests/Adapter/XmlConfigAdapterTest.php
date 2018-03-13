<?php

namespace Misantron\Silex\Provider\Tests\Adapter;


use Misantron\Silex\Provider\Adapter\XmlConfigAdapter;
use Misantron\Silex\Provider\Tests\AdapterTrait;
use PHPUnit\Framework\TestCase;

class XmlConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp()
    {
        $this->adapter = new XmlConfigAdapter();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to parse config file: xmlParseEntityRef: no name
     */
    public function testLoadInvalidConfigFile()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.xml');

        $this->adapter->load($file);
    }

    public function testLoad()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.xml');

        $config = $this->adapter->load($file);

        $this->assertEquals(['foo' => 'bar'], $config);
    }
}