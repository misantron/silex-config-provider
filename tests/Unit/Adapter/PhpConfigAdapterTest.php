<?php

namespace Misantron\Silex\Provider\Tests\Unit\Adapter;

use Misantron\Silex\Provider\Adapter\PhpConfigAdapter;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;
use Misantron\Silex\Provider\Tests\TestCase;
use Misantron\Silex\Provider\Tests\Unit\AdapterTrait;

class PhpConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp(): void
    {
        $this->adapter = new PhpConfigAdapter();
    }

    public function testLoadInvalidConfigFile(): void
    {
        $this->expectException(ConfigurationParseException::class);
        $this->expectExceptionMessage('Invalid configuration file');

        $file = new \SplFileInfo(__DIR__ . '/../../resources/invalid.php');

        $this->adapter->load($file);
    }

    public function testLoad(): void
    {
        $file = new \SplFileInfo(__DIR__ . '/../../resources/base.php');

        $config = $this->adapter->load($file);

        $this->assertSame(['foo' => 'bar'], $config);
    }
}