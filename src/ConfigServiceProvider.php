<?php

namespace Misantron\Silex\Provider;

use Misantron\Silex\Provider\Exception\InvalidConfigurationException;
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
    private $replacements;

    /**
     * @var string
     */
    private $key = 'config';

    /**
     * @param ConfigAdapter $adapter
     * @param array $paths
     * @param array $replacements
     *
     * @throws InvalidConfigurationException
     */
    public function __construct(ConfigAdapter $adapter, array $paths, array $replacements = [])
    {
        $this->config = $this->createConfigFromPaths($adapter, $paths);

        if (empty($this->config)) {
            throw new InvalidConfigurationException('No configuration data provided');
        }

        $this->createReplacements($replacements);
    }

    /**
     * @param string $key
     */
    public function setConfigContainerKey(string $key): void
    {
        $this->key = $key;
    }

    public function register(Container $pimple): void
    {
        if (!isset($pimple[$this->key])) {
            $pimple[$this->key] = new Container();
        }

        // exceptional handler for application debug flag
        if (isset($this->config['debug'])) {
            $pimple['debug'] = $this->config['debug'];
            unset($this->config['debug']);
        }

        $this->doConfigReplacements($pimple);
    }

    /**
     * @param ConfigAdapter $adapter
     * @param array $paths
     * @return array
     */
    private function createConfigFromPaths(ConfigAdapter $adapter, array $paths): array
    {
        $files = array_filter($paths, 'file_exists');

        return array_reduce($files, static function (array $carry, string $path) use ($adapter) {
            $file = new \SplFileInfo($path);
            if ($file->isFile()) {
                $config = $adapter->load($file);
                $carry = array_replace_recursive($carry, $config);
            }
            return $carry;
        }, []);
    }

    /**
     * @param array $replacements
     */
    private function createReplacements(array $replacements): void
    {
        $this->replacements = [];
        foreach ($replacements as $placeholder => $value) {
            $this->replacements['%' . $placeholder . '%'] = $value;
        }
    }

    /**
     * @param Container $app
     */
    private function doConfigReplacements(Container $app): void
    {
        foreach ($this->config as $name => $value) {
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
        if (preg_match('/%env\(([\w]+)\)%/', $value, $matches)) {
            $value = (string) getenv($matches[1] ?? '');
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
            if (\is_array($v)) {
                $value[$k] = $this->doReplacementsInArray($v);
            } elseif (\is_string($v)) {
                $value[$k] = $this->doReplacementsInString($v);
            }
        }
        return $value;
    }
}
