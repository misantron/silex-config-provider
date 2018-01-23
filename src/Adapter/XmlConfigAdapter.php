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
     * {@inheritdoc}
     */
    protected function parse(\SplFileInfo $file): array
    {
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
     * {@inheritdoc}
     */
    protected function configFileExtensions(): array
    {
        return ['xml'];
    }
}