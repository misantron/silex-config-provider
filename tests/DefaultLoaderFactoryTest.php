<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests;

use Misantron\Silex\Provider\Loader\JsonLoader;
use Misantron\Silex\Provider\Loader\PhpLoader;
use Misantron\Silex\Provider\Loader\YamlLoader;
use Misantron\Silex\Provider\DefaultLoaderFactory;
use Misantron\Silex\Provider\Exception\InvalidConfigException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DefaultLoaderFactory::class)]
final class DefaultLoaderFactoryTest extends TestCase
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

        $this->assertInstanceOf($class, $loader);
    }

    public static function createLoaderDataProvider(): \Iterator
    {
        yield 'json' => [
            'config.json',
            JsonLoader::class,
        ];
        yield 'php' => [
            'config.php',
            PhpLoader::class,
        ];
        yield 'yaml' => [
            'config.yaml',
            YamlLoader::class,
        ];
        yield 'yml' => [
            'config.yml',
            YamlLoader::class,
        ];
    }
}
