<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests;

use Misantron\Silex\Provider\DefaultLoaderFactory;
use Misantron\Silex\Provider\Exception\InvalidConfigException;
use Misantron\Silex\Provider\Loader;
use PHPUnit\Framework\TestCase;

class DefaultLoaderFactoryTest extends TestCase
{
    use FakeFileSystemTrait;

    public function testCreateWithNotExistConfigFile(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Config file is not a file');

        $factory = new DefaultLoaderFactory();
        $factory->create($this->getFilePath('not.exist'));
    }

    public function testCreateWithNotReadableConfigFile(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Config file is not readable');

        // create write-only file
        $this->createFile('config.xml', 0222);

        $factory = new DefaultLoaderFactory();
        $factory->create($this->getFilePath('config.xml'));
    }

    public function testCreateWithUnsupportedConfigFile(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Unsupported config file type provided: txt');

        $this->createFile('config.txt');

        $factory = new DefaultLoaderFactory();
        $factory->create($this->getFilePath('config.txt'));
    }

    /**
     * @dataProvider createLoaderDataProvider
     */
    public function testCreate(string $file, string $class): void
    {
        $this->createFile($file);

        $factory = new DefaultLoaderFactory();
        $loader = $factory->create($this->getfilePath($file));

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
