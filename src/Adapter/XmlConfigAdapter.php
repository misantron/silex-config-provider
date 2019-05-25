<?php

namespace Misantron\Silex\Provider\Adapter;

use LibXMLError;
use Misantron\Silex\Provider\ConfigAdapter;
use Misantron\Silex\Provider\Exception\ComponentNotInstalledException;
use Misantron\Silex\Provider\Exception\ConfigurationParseException;
use SimpleXMLElement;
use SplFileInfo;

/**
 * Class XmlConfigAdapter
 * @package Misantron\Silex\Provider\Adapter
 */
class XmlConfigAdapter extends ConfigAdapter
{
    /**
     * @param SplFileInfo $file
     * @return array
     *
     * @throws ConfigurationParseException
     */
    protected function parse(SplFileInfo $file): array
    {
        libxml_use_internal_errors(true);

        $xml = simplexml_load_string(file_get_contents($file->getRealPath()));
        if (!$xml instanceof SimpleXMLElement) {
            $errors = array_map(static function (LibXMLError $error) {
                return trim($error->message);
            }, libxml_get_errors());
            libxml_clear_errors();

            throw new ConfigurationParseException('Unable to parse config file: ' . implode(', ', $errors));
        }

        return json_decode(json_encode($xml), true);
    }

    /**
     * @return array
     */
    protected function configFileExtensions(): array
    {
        return ['xml'];
    }

    /**
     * @throws ComponentNotInstalledException
     */
    protected function assertComponentInstalled()
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('SimpleXMLElement')) {
            throw new ComponentNotInstalledException('SimpleXML component is not installed');
        }
        // @codeCoverageIgnoreEnd
    }
}