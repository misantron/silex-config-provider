<?php

declare(strict_types=1);

namespace Misantron\Silex\Provider\Loader;

use Misantron\Silex\Provider\AbstractLoader;
use Misantron\Silex\Provider\Exception\ConfigParsingException;

/**
 * Class XmlLoader
 * @package Misantron\Silex\Provider\Loader
 */
class XmlLoader extends AbstractLoader
{
    protected function parse(): array
    {
        // @codeCoverageIgnoreStart
        assert(extension_loaded('libxml'));
        assert(extension_loaded('simplexml'));
        // @codeCoverageIgnoreEnd

        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($this->getFileContents());
        if (!$xml instanceof \SimpleXMLElement) {
            $errors = array_map(static function (\LibXMLError $error) {
                return trim($error->message);
            }, libxml_get_errors());

            libxml_clear_errors();

            throw ConfigParsingException::withReason(implode(', ', $errors));
        }

        return json_decode(json_encode($xml, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), true);
    }

    public function getSupportedExtensions(): array
    {
        return ['xml'];
    }
}
