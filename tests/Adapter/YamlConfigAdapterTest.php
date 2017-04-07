<?php

namespace Misantron\Silex\Provider\Tests\Adapter;


use Misantron\Silex\Provider\Adapter\YamlConfigAdapter;
use PHPUnit\Framework\TestCase;

class YamlConfigAdapterTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid config file type provided
     */
    public function testLoadInvalidConfigType()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.ini');

        $adapter = new YamlConfigAdapter();
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

        $file->method('getExtension')->willReturn('yml');
        $file->method('isReadable')->willReturn(false);

        $adapter = new YamlConfigAdapter();
        $adapter->load($file);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to parse config file: Malformed inline YAML string: {bar} test at line 1 (near "foo: {bar} test").
     */
    public function testLoadInvalidConfigFile()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.yml');

        $adapter = new YamlConfigAdapter();
        $adapter->load($file);
    }

    public function testLoad()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.yml');

        $adapter = new YamlConfigAdapter();
        $config = $adapter->load($file);

        $this->assertEquals(['foo' => 'bar'], $config);
    }
}