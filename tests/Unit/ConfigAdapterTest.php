<?php

namespace Misantron\Silex\Provider\Tests\Unit;

use BadMethodCallException;
use Misantron\Silex\Provider\ConfigAdapter;
use Misantron\Silex\Provider\Exception\InvalidConfigurationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

class ConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp()
    {
        $this->adapter = new class extends ConfigAdapter
        {
            /**
             * @param SplFileInfo $file
             * @return array
             */
            protected function parse(SplFileInfo $file): array
            {
                throw new BadMethodCallException('Forbidden');
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

    public function testLoadNotExistsConfigFile()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Configuration file is not readable');

        $file = new SplFileInfo(__DIR__ . '/../resources/not_exists.ext');

        $this->adapter->load($file);
    }

    public function testLoadNotReadableConfig()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Configuration file is not readable');

        /** @var SplFileInfo|MockObject $file */
        $file = $this->createMock(SplFileInfo::class);

        $file
            ->method('isReadable')
            ->willReturn(false);

        $this->adapter->load($file);
    }

    public function testLoadConfigWithInvalidExtension()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Invalid configuration file type provided');

        $file = new SplFileInfo(__DIR__ . '/../resources/invalid.ext');

        $this->adapter->load($file);
    }
}