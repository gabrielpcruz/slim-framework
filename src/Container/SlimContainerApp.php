<?php

namespace SlimFramework\Container;

interface SlimContainerApp
{
    /**
     * @return array
     */
    public function getDefinitions() : array;
}
