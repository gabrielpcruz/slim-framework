<?php

namespace SlimFramework\Provider;

use Adbar\Dot;
use Psr\Container\ContainerInterface;

interface ProviderInterface
{
    public function provide(ContainerInterface $container, Dot $settings): void;
}
