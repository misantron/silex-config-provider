<?php

namespace Misantron\Silex\Provider;


use Misantron\Silex\Provider\Adapter\ConfigAdapterInterface;
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
     * @param ConfigAdapterInterface $adapter
     * @param array $paths
     * @param array $replacements
     * @param string $key
     */
    public function __construct(
        ConfigAdapterInterface $adapter,
        array $paths,
        array $replacements = [],
        string $key = 'config'
    ) {
        $files = array_filter($paths, function ($file) {
            return file_exists($file);
        });
        $config = array_reduce($files, function ($carry, $path) use ($adapter) {
            $file = new \SplFileInfo($path);
            if ($file->isFile()) {
                $config = $adapter->load($file);
                $carry = array_merge_recursive($carry, $config);
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
        $app[$this->key] = new Container();

        foreach ($this->config as $name => $value) {
            if (substr($name, 0, 1) === '%') {
                $this->replacements[$name] = (string)$value;
            }
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
    private function doReplacementsInString(string $value)
    {
        return strtr($value, $this->replacements);
    }

    /**
     * @param array $value
     * @return array
     */
    private function doReplacementsInArray(array $value)
    {
        foreach ($value as $k => $v) {
            if (is_array($value)) {
                $value[$k] = $this->doReplacementsInArray($v);
            } elseif (is_string($value)) {
                $value[$k] = $this->doReplacementsInString($v);
            }
        }
        return $value;
    }
}