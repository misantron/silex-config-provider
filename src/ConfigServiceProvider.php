<?php

namespace Misantron\Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ConfigServiceProvider
 * @package Misantron\Silex\Provider
 */
class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $replacements = [];

    /**
     * @var string
     */
    private $key;

    /**
     * @param ConfigAdapter $adapter
     * @param array $paths
     * @param array $replacements
     * @param string $key
     */
    public function __construct(ConfigAdapter $adapter, array $paths, array $replacements = [], string $key = 'config')
    {
        $files = array_filter($paths, function ($file) {
            return file_exists($file);
        });
        $config = array_reduce($files, function (array $carry, string $path) use ($adapter) {
            $file = new \SplFileInfo($path);
            if ($file->isFile()) {
                $config = $adapter->load($file);
                $carry = array_replace_recursive($carry, $config);
            }
            return $carry;
        }, []);

        if (empty($config)) {
            throw new \RuntimeException('Config is empty');
        }

        $this->key = $key;
        $this->config = $config;

        foreach ($replacements as $key => $value) {
            $this->replacements['%' . $key . '%'] = $value;
        }
    }

    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        if (!isset($app[$this->key])) {
            $app[$this->key] = new Container();
        }

        foreach ($this->config as $name => $value) {
            if ($name === 'debug') {
                $app[$name] = $value;
                continue;
            }
            if (is_array($value)) {
                $app[$this->key][$name] = $this->doReplacementsInArray($value);
            } elseif (is_string($value)) {
                $app[$this->key][$name] = $this->doReplacementsInString($value);
            } else {
                $app[$this->key][$name] = $value;
            }
        }
    }

    /**
     * @param string $value
     * @return string
     */
    private function doReplacementsInString(string $value): string
    {
        // replace special %env(VAR)% syntax with values from the environment
        if (preg_match('/%env\(([a-zA-Z0-9_-]+)\)%/', $value, $matches)) {
            $value = getenv($matches[1]);
        }

        return strtr($value, $this->replacements);
    }

    /**
     * @param array $value
     * @return array
     */
    private function doReplacementsInArray(array $value): array
    {
        foreach ($value as $k => $v) {
            if (is_array($v)) {
                $value[$k] = $this->doReplacementsInArray($v);
            } elseif (is_string($v)) {
                $value[$k] = $this->doReplacementsInString($v);
            }
        }
        return $value;
    }
}
