<?php

namespace Misantron\Silex\Provider\Adapter;


class PhpConfigAdapter implements AdapterInterface
{
    public function load(\SplFileInfo $file) : array
    {
        if ($file->getExtension() !== 'php') {
            throw new \RuntimeException();
        }

        $config = require $file->getRealPath();

        return $config;
    }
}