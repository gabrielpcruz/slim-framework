<?php

namespace SlimFramework\Configuration;

interface ConfigurationInterface
{
    /**
     * @return array
     */
    public function configure() : array;
}