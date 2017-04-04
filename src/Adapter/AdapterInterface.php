<?php

namespace Misantron\Silex\Provider\Adapter;


interface AdapterInterface
{
    /**
     * @param \SplFileInfo $file
     * @return array
     */
    public function load(\SplFileInfo $file) : array;
}