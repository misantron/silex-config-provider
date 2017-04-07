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

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Config is empty
     */
    public function testConstructorWithEmptyConfig()
    {
        /** @var ConfigAdapterInterface|\PHPUnit_Framework_MockObject_MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapterInterface::class);

        $adapter->method('load')->willReturn([]);

        new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/resources/base.php']
        );
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

        $twig = [
            'twig.path' => ['%ROOT_PATH%/app/templates/'],
            'twig.options' => [
                'debug' => true,
                'auto_reload' => true,
                'cache' => '%ROOT_PATH%/app/cache/twig'
            ],
        ];

        /** @var ConfigAdapterInterface|\PHPUnit_Framework_MockObject_MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapterInterface::class);

        $adapter->method('load')->willReturn([
            'debug' => true,
            'base.path' => __DIR__,
            'db.options' => $dbOptions,
            'twig' => $twig
        ]);

        $app = new Application();
        $app->register(new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/resources/base.php']
        ));

        $this->assertEquals(true, $app['debug']);
        $this->assertArrayHasKey('config', $app);
        $this->assertEquals($dbOptions, $app['config']['db.options']);
        $this->assertEquals(__DIR__, $app['config']['base.path']);
        $this->assertEquals($twig, $app['config']['twig']);
    }
}