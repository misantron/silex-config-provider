<?php

namespace Misantron\Silex\Provider\Tests\Adapter;


use Misantron\Silex\Provider\Adapter\PhpConfigAdapter;
use PHPUnit\Framework\TestCase;

class PhpConfigAdapterTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid config file type provided
     */
    public function testLoadInvalidConfigType()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.json');

        $adapter = new PhpConfigAdapter();
        $adapter->load($file);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Config file is not readable
     */
    public function testLoadNotReadableConfig()
    {
        /** @var \SplFileInfo $file */
        $file = $this->createMock(\SplFileInfo::class)
            ->method('isReadable')
            ->willReturn(false);

        $adapter = new PhpConfigAdapter();
        $adapter->load($file);
    }

    public function testLoad()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.php');

        $adapter = new PhpConfigAdapter();
        $config = $adapter->load($file);

        $this->assertEquals(['foo' => 'bar'], $config);
    }
}