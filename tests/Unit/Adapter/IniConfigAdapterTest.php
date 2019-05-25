<?php

namespace Misantron\Silex\Provider\Tests\Unit\Adapter;

use Misantron\Silex\Provider\Adapter\IniConfigAdapter;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;
use Misantron\Silex\Provider\Tests\Unit\AdapterTrait;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

class IniConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp()
    {
        $this->adapter = new IniConfigAdapter();
    }

    public function testLoadInvalidConfigFile()
    {
        $this->expectException(ConfigurationParseException::class);
        $this->expectExceptionMessage('Unable to parse configuration: invalid format');

        $file = new SplFileInfo(__DIR__ . '/../../resources/invalid.ini');

        $this->adapter->load($file);
    }

    public function testLoad()
    {
        $file = new SplFileInfo(__DIR__ . '/../../resources/base.ini');

        $config = $this->adapter->load($file);

        $this->assertEquals(['foo' => 'bar'], $config);
    }
}