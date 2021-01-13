<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests;

use Misantron\Silex\Provider\DefaultLoaderFactory;
use Misantron\Silex\Provider\Exception\InvalidConfigException;
use Misantron\Silex\Provider\Loader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class DefaultLoaderFactoryTest extends TestCase
{
    private static ?vfsStreamDirectory $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = vfsStream::setup();
    }

    public static function tearDownAfterClass(): void
    {
        self::$root = null;
    }

    public function testCreateWithNotExistConfigFile(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Config file is not a file');

        $factory = new DefaultLoaderFactory();
        $factory->create(self::$root->url() . '/not.exist');
    }

    public function testCreateWithNotReadableConfigFile(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Config file is not readable');

        // create write-only file
        vfsStream::newFile('config.xml', 0222)->at(self::$root);

        $factory = new DefaultLoaderFactory();
        $factory->create(self::$root->url() . '/config.xml');
    }

    public function testCreateWithUnsupportedConfigFile(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Unsupported config file type provided: txt');

        vfsStream::newFile('config.txt')->at(self::$root);

        $factory = new DefaultLoaderFactory();
        $factory->create(self::$root->url() . '/config.txt');
    }

    /**
     * @dataProvider createLoaderDataProvider
     */
    public function testCreate(string $file, string $class): void
    {
        vfsStream::newFile($file)->at(self::$root);

        $factory = new DefaultLoaderFactory();
        $loader = $factory->create(self::$root->url() . '/' . $file);

        self::assertSame($class, get_class($loader));
    }

    public function createLoaderDataProvider(): array
    {
        return [
            'ini' => [
                'config.ini',
                Loader\IniLoader::class,
            ],
            'json' => [
                'config.json',
                Loader\JsonLoader::class,
            ],
            'php' => [
                'config.php',
                Loader\PhpLoader::class,
            ],
            'toml' => [
                'config.toml',
                Loader\TomlLoader::class,
            ],
            'xml' => [
                'config.xml',
                Loader\XmlLoader::class,
            ],
            'yaml' => [
                'config.yaml',
                Loader\YamlLoader::class,
            ],
            'yml' => [
                'config.yml',
                Loader\YamlLoader::class,
            ],
        ];
    }
}
