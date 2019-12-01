<?php

namespace Misantron\Silex\Provider\Tests\Unit\Adapter;

use Misantron\Silex\Provider\Adapter\XmlConfigAdapter;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;
use Misantron\Silex\Provider\Tests\TestCase;
use Misantron\Silex\Provider\Tests\Unit\AdapterTrait;

class XmlConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp()
    {
        $this->adapter = new XmlConfigAdapter();
    }

    public function testLoadInvalidConfigFile()
    {
        $this->expectException(ConfigurationParseException::class);
        $this->expectExceptionMessage('Unable to parse config file: xmlParseEntityRef: no name');

        $file = new \SplFileInfo(__DIR__ . '/../../resources/invalid.xml');

        $this->adapter->load($file);
    }

    public function testLoad()
    {
        $file = new \SplFileInfo(__DIR__ . '/../../resources/base.xml');

        $config = $this->adapter->load($file);

        $this->assertSame(['foo' => 'bar'], $config);
    }
}