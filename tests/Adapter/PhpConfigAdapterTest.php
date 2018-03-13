<?php

namespace Misantron\Silex\Provider\Tests\Adapter;


use Misantron\Silex\Provider\Adapter\PhpConfigAdapter;
use Misantron\Silex\Provider\Tests\AdapterTrait;
use PHPUnit\Framework\TestCase;

class PhpConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp()
    {
        $this->adapter = new PhpConfigAdapter();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid config file
     */
    public function testLoadInvalidConfigFile()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.php');

        $this->adapter->load($file);
    }

    public function testLoad()
    {
        $file = new \SplFileInfo(__DIR__ . '/../resources/base.php');

        $config = $this->adapter->load($file);

        $this->assertEquals(['foo' => 'bar'], $config);
    }
}