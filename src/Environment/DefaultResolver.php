<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Environment;

use Misantron\Silex\Provider\Exception\EnvResolvingException;

/**
 * Class DefaultResolver
 * @package Misantron\Silex\Provider\Environment
 */
final class DefaultResolver implements ResolverInterface
{
    private const PATTERN = '/%env\(([a-z]+):([A-Z0-9_]+)\)%/';

    public function resolve(string $value)
    {
        if (preg_match(self::PATTERN, $value, $matches)) {
            $value = $this->process($matches[1], $matches[2]);
        }

        return $value;
    }

    private function process(string $prefix, string $name)
    {
        $value = getenv($name);
        if ($value === false) {
            throw EnvResolvingException::undefinedVariable($name);
        }

        if ($prefix === 'string') {
            return (string) $value;
        }

        if ($prefix === 'bool') {
            return (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        if ($prefix === 'int') {
            return (int) $value;
        }

        if ($prefix === 'float') {
            return (float) $value;
        }

        if ($prefix === 'json') {
            try {
                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw EnvResolvingException::invalidJson($name, $e->getMessage());
            }
        }

        throw EnvResolvingException::unsupportedPrefix($name, $prefix);
    }
}
