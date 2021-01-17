<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider;

use Misantron\Silex\Provider\Exception\InvalidConfigException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ConfigServiceProvider
 * @package Misantron\Silex\Provider
 */
class ConfigServiceProvider implements ServiceProviderInterface
{
    private LoaderFactoryInterface $loaderFactory;

    private array $paths;
    private array $replacements;

    public function __construct(array $paths, array $replacements = [], LoaderFactoryInterface $loaderFactory = null)
    {
        $this->loaderFactory = $loaderFactory ?? new DefaultLoaderFactory();
        $this->paths = $paths;

        $this->createReplacements($replacements);
    }

    public function register(Container $app): void
    {
        $config = array_reduce($this->paths, function (array $carry, string $path) {
            $loader = $this->loaderFactory->create($path);
            $carry = array_replace_recursive($carry, $loader->load());

            return $carry;
        }, []);

        if (\count($config) === 0) {
            throw InvalidConfigException::emptyContent();
        }

        foreach ($config as $name => $value) {
            switch (gettype($value)) {
                case 'array':
                    $app[$name] = $this->doReplacementsInArray($value);
                    break;
                case 'string':
                    $app[$name] = $this->doReplacementsInString($value);
                    break;
                default:
                    $app[$name] = $value;
            }
        }
    }

    private function createReplacements(array $replacements): void
    {
        $this->replacements = [];
        foreach ($replacements as $placeholder => $value) {
            $this->replacements['%' . $placeholder . '%'] = $value;
        }
    }

    private function doReplacementsInString(string $value): string
    {
        // replace special placeholders %env(VAR)% with environment variables
        if (preg_match('/%env\(([\w]+)\)%/', $value, $matches)) {
            $value = (string) getenv($matches[1] ?? '');
        }

        return strtr($value, $this->replacements);
    }

    private function doReplacementsInArray(array $value): array
    {
        foreach ($value as $k => $v) {
            if (\is_array($v)) {
                $value[$k] = $this->doReplacementsInArray($v);
            } elseif (\is_string($v)) {
                $value[$k] = $this->doReplacementsInString($v);
            }
        }
        return $value;
    }
}
