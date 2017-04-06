<?php

namespace Misantron\Silex\Provider\Tests;


use Misantron\Silex\Provider\Adapter\ConfigAdapterInterface;
use Misantron\Silex\Provider\ConfigServiceProvider;
use PHPUnit\Framework\TestCase;
use Silex\Application;

class ConfigServiceProviderTest extends TestCase
{
    public function testDefaultConstructor()
    {
        /** @var ConfigAdapterInterface|\PHPUnit_Framework_MockObject_MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapterInterface::class);

        $adapter->method('load')->willReturn(['foo' => 'bar']);

        $provider = new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/resources/base.php']
        );

        $this->assertAttributeEquals(['foo' => 'bar'], 'config', $provider);
        $this->assertAttributeEquals([], 'replacements', $provider);
        $this->assertAttributeEquals('config', 'key', $provider);
    }

    public function testConstructor()
    {
        /** @var ConfigAdapterInterface|\PHPUnit_Framework_MockObject_MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapterInterface::class);

        $adapter->method('load')->willReturn(['foo' => 'bar']);

        $provider = new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/resources/base.php'],
            ['root' => __DIR__],
            'conf'
        );

        $this->assertAttributeEquals(['foo' => 'bar'], 'config', $provider);
        $this->assertAttributeEquals(['%root%' => __DIR__], 'replacements', $provider);
        $this->assertAttributeEquals('conf', 'key', $provider);
    }

    public function testRegister()
    {
        $dbOptions = [
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'user' => 'root',
            'password' => '',
            'db_name' => 'db_test'
        ];

        /** @var ConfigAdapterInterface|\PHPUnit_Framework_MockObject_MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapterInterface::class);

        $adapter->method('load')->willReturn([
            'debug' => true,
            'db.options' => $dbOptions
        ]);

        $app = new Application();
        $app->register(new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/resources/base.php']
        ));

        $this->assertEquals(true, $app['debug']);
        $this->assertArrayHasKey('config', $app);
        $this->assertEquals($dbOptions, $app['config']['db.options']);
    }
}