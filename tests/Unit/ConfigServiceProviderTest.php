<?php

namespace Misantron\Silex\Provider\Tests\Unit;

use Misantron\Silex\Provider\Adapter\PhpConfigAdapter;
use Misantron\Silex\Provider\ConfigAdapter;
use Misantron\Silex\Provider\ConfigServiceProvider;
use Misantron\Silex\Provider\Exception\InvalidConfigurationException;
use Misantron\Silex\Provider\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Silex\Application;

class ConfigServiceProviderTest extends TestCase
{
    public function testDefaultConstructor()
    {
        /** @var ConfigAdapter|MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapter::class);

        $adapter
            ->expects($this->once())
            ->method('load')
            ->willReturn(['foo' => 'bar']);

        $provider = new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/../resources/base.php']
        );

        $this->assertPropertySame(['foo' => 'bar'], 'config', $provider);
        $this->assertPropertySame([], 'replacements', $provider);
        $this->assertPropertySame('config', 'key', $provider);
    }

    public function testConstructor()
    {
        /** @var ConfigAdapter|MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapter::class);

        $adapter
            ->expects($this->once())
            ->method('load')
            ->willReturn(['foo' => 'bar']);

        $provider = new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/../resources/base.php'],
            ['root' => __DIR__]
        );

        $this->assertPropertySame(['foo' => 'bar'], 'config', $provider);
        $this->assertPropertySame(['%root%' => __DIR__], 'replacements', $provider);
        $this->assertPropertySame('config', 'key', $provider);
    }

    public function testConstructorWithEmptyConfigData()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('No configuration data provided');

        /** @var ConfigAdapter|MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapter::class);

        $adapter
            ->expects($this->once())
            ->method('load')
            ->willReturn([]);

        new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/../resources/base.php']
        );
    }

    public function testSetConfigContainerKey()
    {
        /** @var ConfigAdapter|MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapter::class);

        $adapter
            ->expects($this->once())
            ->method('load')
            ->willReturn(['foo' => 'bar']);

        $provider = new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/../resources/base.php'],
            ['root' => __DIR__]
        );
        $provider->setConfigContainerKey('custom');

        $this->assertPropertySame('custom', 'key', $provider);
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

        $adapter
            ->expects($this->once())
            ->method('load')
            ->willReturn([
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

        $this->assertTrue($app['debug']);
        $this->assertArrayHasKey('config', $app);
        $this->assertSame($dbOptions, $app['config']['db.options']);
        $this->assertSame(__DIR__, $app['config']['base.path']);
        $this->assertSame($twig, $app['config']['twig']);
    }

    public function testRegisterWithEnvironmentVariables()
    {
        $root = realpath(__DIR__ . '/..');

        /** @var ConfigAdapter|MockObject $adapter */
        $adapter = $this->createMock(ConfigAdapter::class);

        $adapter
            ->expects($this->once())
            ->method('load')
            ->willReturn([
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
        $this->assertSame('foo', $app['config']['env.var']);
        $this->assertSame('bar', $app['config']['env.var.1']);
        $this->assertSame('baz', $app['config']['envvar']);
        $this->assertSame($root, $app['config']['envvar1']);
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

        $this->assertTrue($app['debug']);
        $this->assertArrayHasKey('config', $app);

        $this->assertSame('Europe/London', $app['config']['date.timezone']);

        $this->assertSame([
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'user' => 'app',
            'password' => 'root',
            'db_name' => 'db_app'
        ], $app['config']['db.options']);

        $this->assertSame([
            'monolog.logfile' => $root . '/logs/app.log',
            'monolog.name' => 'app'
        ], $app['config']['logger']);
    }
}
