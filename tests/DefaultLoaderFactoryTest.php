<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests;

use Misantron\Silex\Provider\DefaultLoaderFactory;
use Misantron\Silex\Provider\Exception\InvalidConfigException;
use Misantron\Silex\Provider\Loader\JsonLoader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class DefaultLoaderFactoryTest extends TestCase
{
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup();
    }

    public function testCreateWithNotExistConfigFile(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Config file is not a file');

        $factory = new DefaultLoaderFactory();
        $factory->create($this->root->url() . '/not.exist');
    }

    public function testCreateWithNotReadableConfigFile(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Config file is not readable');

        // create write-only file
        vfsStream::newFile('config.xml', 0222)->at($this->root);

        $factory = new DefaultLoaderFactory();
        $factory->create($this->root->url() . '/config.xml');
    }

    public function testCreateWithUnsupportedConfigFile(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Unsupported config file type provided: txt');

        vfsStream::newFile('config.txt')->at($this->root);

        $factory = new DefaultLoaderFactory();
        $factory->create($this->root->url() . '/config.txt');
    }

    public function testCreate(): void
    {
        vfsStream::newFile('config.json')
            ->setContent('{"foo":"bar"}')
            ->at($this->root)
        ;

        $factory = new DefaultLoaderFactory();
        $loader = $factory->create($this->root->url() . '/config.json');

        self::assertInstanceOf(JsonLoader::class, $loader);
    }
}
