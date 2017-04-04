<?php

namespace Misantron\Silex\Provider\Adapter;


interface ConfigAdapterInterface
{
    /**
     * @param \SplFileInfo $file
     * @return array
     */
    public function load(\SplFileInfo $file) : array;
}