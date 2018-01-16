<?php

namespace Misantron\Silex\Provider\Adapter;


/**
 * Class XmlConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class XmlConfigAdapter implements ConfigAdapterInterface
{
    /**
     * @param \SplFileInfo $file
     * @return array
     */
    public function load(\SplFileInfo $file): array
    {
        if (!$file->isReadable()) {
            throw new \RuntimeException('Config file is not readable');
        }
        if ($file->getExtension() !== 'xml') {
            throw new \RuntimeException('Invalid config file type provided');
        }

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
}