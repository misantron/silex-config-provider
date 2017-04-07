<?php

namespace Misantron\Silex\Provider\Tests\Adapter;


use Misantron\Silex\Provider\Adapter\IniConfigAdapter;
use PHPUnit\Framework\TestCase;

class IniConfigAdapterTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid config file type provided
     */
    public function testLoadInvalidConfigType()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.json');

        $adapter = new IniConfigAdapter();
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

        $file->method('getExtension')->willReturn('ini');
        $file->method('isReadable')->willReturn(false);

        $adapter = new IniConfigAdapter();
        $adapter->load($file);
    }

    public function testLoad()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.ini');

        $adapter = new IniConfigAdapter();
        $config = $adapter->load($file);

        $this->assertEquals(['foo' => 'bar'], $config);
    }
}