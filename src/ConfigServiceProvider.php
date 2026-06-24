<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider;

use Misantron\Silex\Provider\Environment\DefaultResolver;
use Misantron\Silex\Provider\Environment\ResolverInterface;
use Misantron\Silex\Provider\Exception\InvalidConfigException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @package Misantron\Silex\Provider
 */
class ConfigServiceProvider implements ServiceProviderInterface
{
    private readonly LoaderFactoryInterface $loaderFactory;

    private readonly ResolverInterface $environmentResolver;

    private array $replacements;

    public function __construct(
        private readonly array $paths,
        array $replacements = [],
        private array $aliases = [],
        ?LoaderFactoryInterface $loaderFactory = null,
        ?ResolverInterface $environmentResolver = null,
    ) {
        $this->loaderFactory = $loaderFactory ?? new DefaultLoaderFactory();
        $this->environmentResolver = $environmentResolver ?? new DefaultResolver();

        $this->createReplacements($replacements);
    }

    public function register(Container $pimple): void
    {
        $config = array_reduce($this->paths, function (array $carry, string $path) {
            $loader = $this->loaderFactory->create($path);

            return array_replace_recursive($carry, $loader->load());
        }, []);

        if (\count($config) === 0) {
            throw InvalidConfigException::emptyContent();
        }

        foreach ($config as $name => $value) {
            // replace key with alias if defined
            $key = $this->aliases[$name] ?? $name;
            $pimple[$key] = match (true) {
                is_array($value) => $this->doReplacementsInArray($value),
                is_string($value) => $this->doReplacementsInString($value),
                default => $value,
            };
        }
    }

    private function createReplacements(array $replacements): void
    {
        $this->replacements = [];
        foreach ($replacements as $placeholder => $value) {
            $this->replacements['%' . $placeholder . '%'] = $value;
        }
    }

    private function doReplacementsInString(string $value)
    {
        $resolved = $this->environmentResolver->resolve($value);
        if (\is_string($resolved)) {
            return strtr($resolved, $this->replacements);
        }

        return $resolved;
    }

    private function doReplacementsInArray(array $list): array
    {
        foreach ($list as $name => $value) {
            // replace key with alias if defined
            $key = $this->aliases[$name] ?? $name;
            if (\is_array($value)) {
                $list[$key] = $this->doReplacementsInArray($value);
            } elseif (\is_string($value)) {
                $list[$key] = $this->doReplacementsInString($value);
            }
        }

        return $list;
    }
}
