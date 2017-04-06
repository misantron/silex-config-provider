<?php

namespace Misantron\Silex\Provider\Tests;


use Misantron\Silex\Provider\Adapter\ConfigAdapterInterface;
use Misantron\Silex\Provider\ConfigServiceProvider;
use PHPUnit\Framework\TestCase;

class ConfigServiceProviderTest extends TestCase
{
    public function testDefaultConstructor()
    {
        /** @var ConfigAdapterInterface $adapter */
        $adapter = $this->createMock(ConfigAdapterInterface::class)
            ->method('load')
            ->willReturn(['foo' => 'bar']);

        $provider = new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/resources/base.php']
        );

        $this->assertAttributeEquals(['foo' => 'bar'], 'config', $provider);
        $this->assertAttributeEquals([], 'replacements', $provider);
        $this->assertAttributeEquals('config', 'key', $provider);
    }

    public function testConstructor()
    {
        /** @var ConfigAdapterInterface $adapter */
        $adapter = $this->createMock(ConfigAdapterInterface::class)
            ->method('load')
            ->willReturn(['foo' => 'bar']);

        $provider = new ConfigServiceProvider(
            $adapter,
            [__DIR__ . '/resources/base.php'],
            ['root' => __DIR__],
            'conf'
        );

        $this->assertAttributeEquals(['foo' => 'bar'], 'config', $provider);
        $this->assertAttributeEquals(['%root%' => __DIR__], 'replacements', $provider);
        $this->assertAttributeEquals('conf', 'key', $provider);
    }
}