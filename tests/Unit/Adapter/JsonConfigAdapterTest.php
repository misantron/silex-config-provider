<?php

namespace Misantron\Silex\Provider\Tests\Unit\Adapter;

use Misantron\Silex\Provider\Adapter\JsonConfigAdapter;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;
use Misantron\Silex\Provider\Tests\TestCase;
use Misantron\Silex\Provider\Tests\Unit\AdapterTrait;

class JsonConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp()
    {
        $this->adapter = new JsonConfigAdapter();
    }

    public function testLoadInvalidConfigFile()
    {
        $this->expectException(ConfigurationParseException::class);
        $this->expectExceptionMessage('Unable to parse json file: Syntax error');

        $file = new \SplFileInfo(__DIR__ . '/../../resources/invalid.json');

        $this->adapter->load($file);
    }

    public function testLoad()
    {
        $file = new \SplFileInfo(__DIR__ . '/../../resources/base.json');

        $config = $this->adapter->load($file);

        $this->assertSame(['foo' => 'bar'], $config);
    }
}