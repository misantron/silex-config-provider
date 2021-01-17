<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests;

/**
 * Trait AssertObjectPropertyTrait
 * @package Misantron\Silex\Provider\Tests
 */
trait AssertObjectPropertyTrait
{
    public function assertPropertySame($expected, string $attributeName, object $actual): void
    {
        static::assertSame($expected, $this->extractPropertyValue($actual, $attributeName));
    }

    public function assertPropertyInstanceOf(string $expected, string $attributeName, object $actual): void
    {
        static::assertInstanceOf($expected, $this->extractPropertyValue($actual, $attributeName));
    }

    private function extractPropertyValue(object $obj, string $name)
    {
        $reflection = new \ReflectionClass($obj);
        $prop = $reflection->getProperty($name);
        $prop->setAccessible(true);

        return $prop->getValue($obj);
    }
}
