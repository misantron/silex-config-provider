<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests;

use Misantron\Silex\Provider\ConfigServiceProvider;
use Misantron\Silex\Provider\DefaultLoaderFactory;
use Misantron\Silex\Provider\Exception\InvalidConfigException;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Silex\Application;

class ConfigServiceProviderTest extends TestCase
{
    use AssertObjectPropertyTrait;
    use FakeFileSystemTrait;

    public function testDefaultConstructor(): void
    {
        $this->createFile('default.json', null, '{"foo":"bar"}');

        $provider = new ConfigServiceProvider(
            [$this->getFilePath('default.json')]
        );

        $this->assertPropertyInstanceOf(DefaultLoaderFactory::class, 'loaderFactory', $provider);
        $this->assertPropertySame([$this->getFilePath('default.json')], 'paths', $provider);
        $this->assertPropertySame([], 'replacements', $provider);
    }

    public function testConstructor(): void
    {
        $this->createFile('base.json', null, '{"foo":"bar"}');

        $provider = new ConfigServiceProvider(
            [$this->getFilePath('base.json')],
            ['APP_ROOT' => __DIR__]
        );

        $this->assertPropertySame([$this->getFilePath('base.json')], 'paths', $provider);
        $this->assertPropertySame(['%APP_ROOT%' => __DIR__], 'replacements', $provider);
    }

    public function testRegisterWithEmptyConfigData(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('No configuration data provided');

        $this->createFile('empty.json', null, '{}');

        $provider = new ConfigServiceProvider(
            [$this->getFilePath('empty.json')]
        );
        $provider->register(new Container());
    }

    public function testRegister(): void
    {
        $this->createFile('extended.json', null, json_encode([
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
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        $app = new Application(['debug' => false]);
        $app->register(new ConfigServiceProvider(
            [$this->getFilePath('extended.json')]
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

        $this->createFile('env.json', null, json_encode([
            'env.var' => '%env(ENV_VAR)%',
            'env.var.1' => '%env(ENV_VAR_1)%',
            'envvar' => '%env(ENVVAR)%',
            'envvar1' => '%env(ENVVAR1)%',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        $app = new Application();
        $app->register(new ConfigServiceProvider(
            [$this->getFilePath('env.json')],
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

        $this->createFile('common.json', null, json_encode([
            'debug' => true,
            'date.timezone' => 'Europe/London',
            'db.options' => [
                'driver' => 'pdo_mysql',
                'host' => 'localhost',
                'user' => 'root',
                'password' => '',
            ],
            'service' => [
                'name' => 'Test',
                'api' => [
                    'base_url' => 'https://api.example.com',
                    'headers' => [
                        'Content-type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'credentials' => [
                        'login' => 'user',
                        'password' => '$ecret',
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        $this->createFile('app.json', null, json_encode([
            'db.options' => [
                'db_name' => 'db_app',
                'user' => 'app',
                'password' => 'root',
            ],
            'logger' => [
                'monolog.logfile' => '%ROOT_PATH%/logs/app.log',
                'monolog.name' => 'app'
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        $app = new Application(['debug' => false]);
        $app->register(new ConfigServiceProvider(
            [
                $this->getFilePath('common.json'),
                $this->getFilePath('app.json'),
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
            'name' => 'Test',
            'api' => [
                'base_url' => 'https://api.example.com',
                'headers' => [
                    'Content-type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'credentials' => [
                    'login' => 'user',
                    'password' => '$ecret',
                ],
            ],
        ], $app['service']);

        self::assertSame([
            'monolog.logfile' => $root . '/logs/app.log',
            'monolog.name' => 'app'
        ], $app['logger']);
    }

    public function testRegisterWithKeyAlias(): void
    {
        $this->createFile('aliases.json', null, json_encode([
            'db.credentials' => [
                'driver' => 'pdo_mysql',
                'host' => 'localhost',
                'name' => 'db_app',
                'user' => 'app',
                'password' => 'root',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        $provider = new ConfigServiceProvider(
            [
                $this->getFilePath('aliases.json'),
            ],
            [],
            [
                'db.credentials' => 'db.options',
            ]
        );

        $app = new Application();
        $app->register($provider);

        self::assertArrayHasKey('db.options', $app);
        self::assertArrayNotHasKey('db.credentials', $app);
    }
}
