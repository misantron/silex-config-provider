<?php

namespace Misantron\Silex\Provider\Tests\Adapter;


use Misantron\Silex\Provider\Adapter\XmlConfigAdapter;
use PHPUnit\Framework\TestCase;

class XmlConfigAdapterTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid config file type provided
     */
    public function testLoadInvalidConfigType()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.yml');

        $adapter = new XmlConfigAdapter();
        $adapter->load($file);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Config file is not readable
     */
    public function testLoadNotReadableConfig()
    {
        /** @var \SplFileInfo|\PHPUnit_Framework_MockObject_MockObject $file */
        $file = $this->createMock(\SplFileInfo::class);

        $file->method('getExtension')->willReturn('xml');
        $file->method('isReadable')->willReturn(false);

        $adapter = new XmlConfigAdapter();
        $adapter->load($file);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to parse config file: xmlParseEntityRef: no name
     */
    public function testLoadInvalidConfigFile()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.xml');

        $adapter = new XmlConfigAdapter();
        $adapter->load($file);
    }

    public function testLoad()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.xml');

        $adapter = new XmlConfigAdapter();
        $config = $adapter->load($file);

        $this->assertEquals(['foo' => 'bar'], $config);
    }
}