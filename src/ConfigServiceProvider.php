<?php

namespace Misantron\Silex\Provider;


use Misantron\Silex\Provider\Adapter\AdapterInterface;
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
     * @param AdapterInterface $adapter
     * @param array $paths
     * @param array $replacements
     */
    public function __construct(AdapterInterface $adapter, array $paths, array $replacements = [])
    {
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
            throw new \RuntimeException('');
        }

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
        $app['config'] = new Container();

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
            $app['config'][$name] = $this->doReplacements($value);
        }
    }

    private function doReplacements($value)
    {
        if (empty($this->replacements)) {
            return $value;
        }
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->doReplacements($v);
            }
            return $value;
        } else if (is_string($value)) {
            return strtr($value, $this->replacements);
        }
        return $value;
    }
}