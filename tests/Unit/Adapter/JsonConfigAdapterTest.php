<?php

namespace Misantron\Silex\Provider\Tests\Unit\Adapter;

use Misantron\Silex\Provider\Adapter\JsonConfigAdapter;
use Misantron\Silex\Provider\Tests\Unit\AdapterTrait;
use PHPUnit\Framework\TestCase;

class JsonConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp()
    {
        $this->adapter = new JsonConfigAdapter();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to parse JSON file: Syntax error
     */
    public function testLoadInvalidConfigFile()
    {
        $file = new \SplFileInfo(__DIR__ . '/../../resources/invalid.json');

        $this->adapter->load($file);
    }

    public function testLoad()
    {
        $file = new \SplFileInfo(__DIR__ . '/../../resources/base.json');

        $config = $this->adapter->load($file);

        $this->assertEquals(['foo' => 'bar'], $config);
    }
}