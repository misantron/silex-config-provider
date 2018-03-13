<?php

namespace Misantron\Silex\Provider\Tests;


use Misantron\Silex\Provider\ConfigAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp()
    {
        $this->adapter = new class extends ConfigAdapter
        {
            /**
             * @param \SplFileInfo $file
             * @return array
             */
            protected function parse(\SplFileInfo $file): array
            {
                throw new \BadMethodCallException('Forbidden');
            }

            /**
             * @return array
             */
            protected function configFileExtensions(): array
            {
                return [];
            }
        };
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Config file is not readable
     */
    public function testLoadNotReadableConfig()
    {
        /** @var \SplFileInfo|MockObject $file */
        $file = $this->createMock(\SplFileInfo::class);

        $file->method('getExtension')->willReturn('ini');
        $file->method('isReadable')->willReturn(false);

        $this->adapter->load($file);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid config file type provided
     */
    public function testLoadConfigWithInvalidExtension()
    {
        $file = new \SplFileInfo(__DIR__ . '/resources/invalid.ext');

        $this->adapter->load($file);
    }
}