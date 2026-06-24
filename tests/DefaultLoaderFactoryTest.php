<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests;

use Misantron\Silex\Provider\DefaultLoaderFactory;
use Misantron\Silex\Provider\Exception\InvalidConfigException;
use Misantron\Silex\Provider\Loader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DefaultLoaderFactory::class)]
class DefaultLoaderFactoryTest extends TestCase
{
    use FakeFileSystemTrait;

    public function testCreateWithNotExistConfigFile(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Config is not a file');

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

    #[DataProvider('createLoaderDataProvider')]
    public function testCreate(string $file, string $class): void
    {
        $this->createFile($file);

        $factory = new DefaultLoaderFactory();
        $loader = $factory->create($this->getfilePath($file));

        self::assertSame($class, get_class($loader));
    }

    public static function createLoaderDataProvider(): array
    {
        return [
            'json' => [
                'config.json',
                Loader\JsonLoader::class,
            ],
            'php' => [
                'config.php',
                Loader\PhpLoader::class,
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
