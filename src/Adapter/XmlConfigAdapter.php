<?php

namespace Misantron\Silex\Provider\Adapter;

use Misantron\Silex\Provider\ConfigAdapter;

/**
 * Class XmlConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class XmlConfigAdapter extends ConfigAdapter
{
    /**
     * @param \SplFileInfo $file
     * @return array
     */
    protected function parse(\SplFileInfo $file): array
    {
        $this->assertComponentInstalled();

        libxml_use_internal_errors(true);

        $xml = simplexml_load_file($file->getRealPath());
        if (!$xml instanceof \SimpleXMLElement) {
            $errors = array_map(function (\LibXMLError $error) {
                return trim($error->message);
            }, libxml_get_errors());
            libxml_clear_errors();

            throw new \RuntimeException('Unable to parse config file: ' . implode(', ', $errors));
        }

        $config = json_decode(json_encode($xml), true);

        return $config;
    }

    /**
     * @return array
     */
    protected function configFileExtensions(): array
    {
        return ['xml'];
    }

    /**
     *
     */
    private function assertComponentInstalled()
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('SimpleXMLElement')) {
            throw new \RuntimeException('SimpleXML component is not installed');
        }
        // @codeCoverageIgnoreEnd
    }
}