<?php

namespace SlimFramework\Twig;

use SlimFramework\Slim;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IsProductionTwigExtension extends AbstractExtension
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'is_production';
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_production', [$this, 'isProduction']),
        ];
    }

    /**
     * @return bool
     */
    public function isProduction(): bool
    {
        return Slim::isProduction();
    }
}
