<?php

namespace Misantron\Silex\Provider\Tests;

trait AssertObjectPropertyTrait
{
    public function assertPropertySame($expected, string $attributeName, $actual)
    {
        static::assertSame($expected, $this->getObjectPropertyValue($actual, $attributeName));
    }

    public function assertPropertyInstanceOf(string $expected, string $attributeName, $actual)
    {
        static::assertInstanceOf($expected, $this->getObjectPropertyValue($actual, $attributeName));
    }

    public function assertPropertyNull(string $attributeName, $actual)
    {
        static::assertNull($this->getObjectPropertyValue($actual, $attributeName));
    }

    private function getObjectPropertyValue($obj, string $name)
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getProperty($name);
        $method->setAccessible(true);

        return $method->getValue($obj);
    }
}
