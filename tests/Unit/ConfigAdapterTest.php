<?php

namespace Misantron\Silex\Provider\Tests\Unit;

use Misantron\Silex\Provider\ConfigAdapter;
use Misantron\Silex\Provider\Exception\InvalidConfigurationException;
use Misantron\Silex\Provider\Tests\TestCase;

class ConfigAdapterTest extends TestCase
{
    use AdapterTrait;

    protected function setUp(): void
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

    public function testLoadNotExistsConfigFile(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Configuration file is not readable');

        $file = new \SplFileInfo(__DIR__ . '/../resources/not_exists.ext');

        $this->adapter->load($file);
    }

    public function testLoadNotReadableConfig(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Configuration file is not readable');

        $file = $this->createMock(\SplFileInfo::class);
        $file
            ->expects(self::once())
            ->method('isReadable')
            ->willReturn(false);

        $this->adapter->load($file);
    }

    public function testLoadConfigWithInvalidExtension(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Invalid configuration file type provided');

        $file = new \SplFileInfo(__DIR__ . '/../resources/invalid.ext');

        $this->adapter->load($file);
    }
}
