<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Tests\Environment;

use Misantron\Silex\Provider\Environment\DefaultResolver;
use Misantron\Silex\Provider\Exception\EnvResolvingException;
use PHPUnit\Framework\TestCase;

class DefaultResolverTest extends TestCase
{
    public function testResolveUndefinedVariable(): void
    {
        $this->expectException(EnvResolvingException::class);
        $this->expectExceptionMessage('Environment variable ENV_VAR not found');

        $resolver = new DefaultResolver();
        $resolver->resolve('%env(string:ENV_VAR)%');
    }

    public function testResolveStringVariable(): void
    {
        putenv('ENV_VAR=test');

        $resolver = new DefaultResolver();
        $value = $resolver->resolve('%env(string:ENV_VAR)%');

        self::assertSame('test', $value);
    }

    public function testResolveJsonVariableWithInvalidContent(): void
    {
        $this->expectException(EnvResolvingException::class);
        $this->expectExceptionMessage('Invalid JSON value ENV_VAR: Syntax error');

        putenv('ENV_VAR=["foo":"bar"]');

        $resolver = new DefaultResolver();
        $resolver->resolve('%env(json:ENV_VAR)%');
    }

    public function testResolveJsonVariable(): void
    {
        putenv('ENV_VAR={"foo":"bar"}');

        $resolver = new DefaultResolver();
        $value = $resolver->resolve('%env(json:ENV_VAR)%');

        self::assertSame(['foo' => 'bar'], $value);
    }

    public function testResolveIntegerVariable(): void
    {
        putenv('ENV_VAR=42');

        $resolver = new DefaultResolver();
        $value = $resolver->resolve('%env(int:ENV_VAR)%');

        self::assertSame(42, $value);
    }

    public function testResolveBooleanVariable(): void
    {
        putenv('ENV_VAR=true');

        $resolver = new DefaultResolver();
        $value = $resolver->resolve('%env(bool:ENV_VAR)%');

        self::assertTrue($value);
    }

    public function testResolveFloatVariable(): void
    {
        putenv('ENV_VAR=5.2');

        $resolver = new DefaultResolver();
        $value = $resolver->resolve('%env(float:ENV_VAR)%');

        self::assertSame(5.2, $value);
    }

    public function testResolveUnsupportedVariable(): void
    {
        $this->expectException(EnvResolvingException::class);
        $this->expectExceptionMessage('Unsupported prefix for ENV_VAR provided: default');

        putenv('ENV_VAR=foo');

        $resolver = new DefaultResolver();
        $resolver->resolve('%env(default:ENV_VAR)%');
    }
}
