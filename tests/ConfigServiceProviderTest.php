<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests;

use Misantron\Silex\Provider\ConfigServiceProvider;
use Misantron\Silex\Provider\DefaultLoaderFactory;
use Misantron\Silex\Provider\Exception\InvalidConfigException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Silex\Application;

class ConfigServiceProviderTest extends TestCase
{
    use AssertObjectPropertyTrait;

    private static ?vfsStreamDirectory $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = vfsStream::setup('root', null, [
            'base.json' => '{"foo":"bar"}',
            'empty.json' => '{}',
            'extended.json' => json_encode([
                'debug' => true,
                'db.options' => [
                    'driver' => 'pdo_mysql',
                    'host' => 'localhost',
                    'user' => 'root',
                    'password' => '',
                    'db_name' => 'db_test',
                ],
                'twig.options' => [
                    'debug' => true,
                    'auto_reload' => true,
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'env.json' => json_encode([
                'env.var' => '%env(ENV_VAR)%',
                'env.var.1' => '%env(ENV_VAR_1)%',
                'envvar' => '%env(ENVVAR)%',
                'envvar1' => '%env(ENVVAR1)%',
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        self::$root = null;
    }

    public function testDefaultConstructor(): void
    {
        $provider = new ConfigServiceProvider(
            [self::$root->url() . '/base.json']
        );

        $this->assertPropertyInstanceOf(DefaultLoaderFactory::class, 'loaderFactory', $provider);
        $this->assertPropertySame([self::$root->url() . '/base.json'], 'paths', $provider);
        $this->assertPropertySame([], 'replacements', $provider);
    }

    public function testConstructor(): void
    {
        $provider = new ConfigServiceProvider(
            [self::$root->url() . '/base.json'],
            ['app.root' => __DIR__]
        );

        $this->assertPropertySame([self::$root->url() . '/base.json'], 'paths', $provider);
        $this->assertPropertySame(['%app.root%' => __DIR__], 'replacements', $provider);
    }

    public function testRegisterWithEmptyConfigData(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('No configuration data provided');

        $provider = new ConfigServiceProvider(
            [self::$root->url() . '/empty.json']
        );
        $provider->register(new Container());
    }

    public function testRegister(): void
    {
        $app = new Application(['debug' => false]);
        $app->register(new ConfigServiceProvider(
            [self::$root->url() . '/extended.json']
        ));

        self::assertTrue($app['debug']);
        self::assertSame([
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'user' => 'root',
            'password' => '',
            'db_name' => 'db_test',
        ], $app['db.options']);
        self::assertSame([
            'debug' => true,
            'auto_reload' => true,
        ], $app['twig.options']);
    }

    public function testRegisterWithEnvironmentVariables(): void
    {
        $root = realpath(__DIR__ . '/..');

        putenv('ENV_VAR=foo');
        putenv('ENV_VAR_1=bar');
        putenv('ENVVAR=baz');
        putenv('ENVVAR1=%ROOT_PATH%');

        $app = new Application();
        $app->register(new ConfigServiceProvider(
            [self::$root->url() . '/env.json'],
            ['ROOT_PATH' => $root]
        ));

        self::assertSame('foo', $app['env.var']);
        self::assertSame('bar', $app['env.var.1']);
        self::assertSame('baz', $app['envvar']);
        self::assertSame($root, $app['envvar1']);
    }

    public function testRegisterWithConfigFilesMergeAndReplacements(): void
    {
        $root = realpath(__DIR__ . '/..');

        $app = new Application(['debug' => false]);
        $app->register(new ConfigServiceProvider(
            [
                __DIR__ . '/resources/common.php',
                __DIR__ . '/resources/app.php',
            ],
            [
                'ROOT_PATH' => $root,
            ]
        ));

        self::assertTrue($app['debug']);

        self::assertSame('Europe/London', $app['date.timezone']);

        self::assertSame([
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'user' => 'app',
            'password' => 'root',
            'db_name' => 'db_app'
        ], $app['db.options']);

        self::assertSame([
            'monolog.logfile' => $root . '/logs/app.log',
            'monolog.name' => 'app'
        ], $app['logger']);
    }
}
