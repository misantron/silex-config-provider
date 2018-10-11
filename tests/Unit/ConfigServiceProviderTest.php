<?php

namespace Misantron\Silex\Provider\Tests\Unit;

use Misantron\Silex\Provider\Adapter\PhpConfigAdapter;
use Misantron\Silex\Provider\ConfigAdapter;
use Misantron\Silex\Provider\ConfigServiceProvider;
use Misantron\Silex\Provider\Exception\InvalidConfigurationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Silex\Application;

class ConfigServiceProviderTest extends TestCase
{
    public function testDefaultConstructor()
    {
        /** @var ConfigAdapter|MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapter::class);

        $adapter->method('load')->willReturn(['foo' => 'bar']);

        $provider = new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/../resources/base.php']
        );

        $this->assertAttributeEquals(['foo' => 'bar'], 'config', $provider);
        $this->assertAttributeEquals([], 'replacements', $provider);
        $this->assertAttributeEquals('config', 'key', $provider);
    }

    public function testConstructor()
    {
        /** @var ConfigAdapter|MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapter::class);

        $adapter->method('load')->willReturn(['foo' => 'bar']);

        $provider = new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/../resources/base.php'],
            ['root' => __DIR__]
        );

        $this->assertAttributeEquals(['foo' => 'bar'], 'config', $provider);
        $this->assertAttributeEquals(['%root%' => __DIR__], 'replacements', $provider);
        $this->assertAttributeEquals('config', 'key', $provider);
    }

    public function testConstructorWithEmptyConfigData()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('No configuration data provided');

        /** @var ConfigAdapter|MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapter::class);

        $adapter->method('load')->willReturn([]);

        new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/../resources/base.php']
        );
    }

    public function testSetConfigContainerKey()
    {
        /** @var ConfigAdapter|MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapter::class);

        $adapter->method('load')->willReturn(['foo' => 'bar']);

        $provider = new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/../resources/base.php'],
            ['root' => __DIR__]
        );
        $provider->setConfigContainerKey('custom');

        $this->assertAttributeEquals('custom', 'key', $provider);
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
            'twig.options' => [
                'debug' => true,
                'auto_reload' => true,
            ],
        ];

        /** @var ConfigAdapter|MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapter::class);

        $adapter->method('load')->willReturn([
            'debug' => true,
            'base.path' => __DIR__,
            'db.options' => $dbOptions,
            'twig' => $twig
        ]);

        $app = new Application(['debug' => false]);
        $app->register(new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/../resources/base.php']
        ));

        $this->assertEquals(true, $app['debug']);
        $this->assertArrayHasKey('config', $app);
        $this->assertEquals($dbOptions, $app['config']['db.options']);
        $this->assertEquals(__DIR__, $app['config']['base.path']);
        $this->assertEquals($twig, $app['config']['twig']);
    }

    public function testRegisterWithEnvironmentVariables()
    {
        $root = realpath(__DIR__ . '/..');

        /** @var ConfigAdapter|MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapter::class);

        $adapter->method('load')->willReturn([
            'env.var' => '%env(ENV_VAR)%',
            'env.var.1' => '%env(ENV_VAR_1)%',
            'envvar' => '%env(ENVVAR)%',
            'envvar1' => '%env(ENVVAR1)%',
        ]);

        putenv('ENV_VAR=foo');
        putenv('ENV_VAR_1=bar');
        putenv('ENVVAR=baz');
        putenv('ENVVAR1=%ROOT_PATH%');

        $app = new Application(['debug' => false]);
        $app->register(new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/../resources/base.php'],
            ['ROOT_PATH' => $root]
        ));

        $this->assertArrayHasKey('config', $app);
        $this->assertEquals('foo', $app['config']['env.var']);
        $this->assertEquals('bar', $app['config']['env.var.1']);
        $this->assertEquals('baz', $app['config']['envvar']);
        $this->assertEquals($root, $app['config']['envvar1']);
    }

    public function testRegisterWithConfigFilesMergeAndReplacements()
    {
        $root = realpath(__DIR__ . '/..');

        $app = new Application(['debug' => false]);
        $app->register(new ConfigServiceProvider(
            new PhpConfigAdapter(),
            [
                __DIR__ . '/../resources/common.php',
                __DIR__ . '/../resources/app.php',
            ],
            [
                'ROOT_PATH' => $root,
            ]
        ));

        $this->assertEquals(true, $app['debug']);
        $this->assertArrayHasKey('config', $app);

        $this->assertEquals('Europe/London', $app['config']['date.timezone']);

        $this->assertEquals([
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'user' => 'app',
            'password' => 'root',
            'db_name' => 'db_app'
        ], $app['config']['db.options']);

        $this->assertEquals([
            'monolog.logfile' => $root . '/logs/app.log',
            'monolog.name' => 'app'
        ], $app['config']['logger']);
    }
}
