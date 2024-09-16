<?php

namespace App\src\Container;

interface SlimContainerApp
{
    /**
     * @return array
     */
    public function getDefinitions() : array;
}
