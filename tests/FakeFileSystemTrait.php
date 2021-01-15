<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

trait FakeFileSystemTrait
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

    private function getFilePath(string $name): string
    {
        return self::$root->url() . DIRECTORY_SEPARATOR . $name;
    }

    private function createFile(string $name, int $permissions = null, string $content = ''): void
    {
        vfsStream::newFile($name, $permissions)
            ->setContent($content)
            ->at(self::$root)
        ;
    }
}
