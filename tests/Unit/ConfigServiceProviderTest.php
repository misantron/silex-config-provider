<?php

namespace Misantron\Silex\Provider\Tests\Unit;

use Misantron\Silex\Provider\Adapter\PhpConfigAdapter;
use Misantron\Silex\Provider\ConfigAdapter;
use Misantron\Silex\Provider\ConfigServiceProvider;
use Misantron\Silex\Provider\Exception\InvalidConfigurationException;
use Misantron\Silex\Provider\Tests\TestCase;
use Silex\Application;

class ConfigServiceProviderTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        $adapter = $this->createMock(ConfigAdapter::class);
        $adapter
            ->expects(self::once())
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

    public function testConstructor(): void
    {
        $adapter = $this->createMock(ConfigAdapter::class);
        $adapter
            ->expects(self::once())
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

    public function testConstructorWithEmptyConfigData(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('No configuration data provided');

        $adapter = $this->createMock(ConfigAdapter::class);
        $adapter
            ->expects(self::once())
            ->method('load')
            ->willReturn([]);

        new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/../resources/base.php']
        );
    }

    public function testSetConfigContainerKey(): void
    {
        $adapter = $this->createMock(ConfigAdapter::class);
        $adapter
            ->expects(self::once())
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

    public function testRegister(): void
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

        $adapter = $this->createMock(ConfigAdapter::class);
        $adapter
            ->expects(self::once())
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

        self::assertTrue($app['debug']);
        self::assertArrayHasKey('config', $app);
        self::assertSame($dbOptions, $app['config']['db.options']);
        self::assertSame(__DIR__, $app['config']['base.path']);
        self::assertSame($twig, $app['config']['twig']);
    }

    public function testRegisterWithEnvironmentVariables(): void
    {
        $root = realpath(__DIR__ . '/..');

        $adapter = $this->createMock(ConfigAdapter::class);
        $adapter
            ->expects(self::once())
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

        self::assertArrayHasKey('config', $app);
        self::assertSame('foo', $app['config']['env.var']);
        self::assertSame('bar', $app['config']['env.var.1']);
        self::assertSame('baz', $app['config']['envvar']);
        self::assertSame($root, $app['config']['envvar1']);
    }

    public function testRegisterWithConfigFilesMergeAndReplacements(): void
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

        self::assertTrue($app['debug']);
        self::assertArrayHasKey('config', $app);

        self::assertSame('Europe/London', $app['config']['date.timezone']);

        self::assertSame([
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'user' => 'app',
            'password' => 'root',
            'db_name' => 'db_app'
        ], $app['config']['db.options']);

        self::assertSame([
            'monolog.logfile' => $root . '/logs/app.log',
            'monolog.name' => 'app'
        ], $app['config']['logger']);
    }
}
