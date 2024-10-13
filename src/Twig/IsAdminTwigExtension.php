<?php

namespace SlimFramework\Twig;

use SlimFramework\Session\Session;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IsAdminTwigExtension extends AbstractExtension
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
            new TwigFunction('is_admin', [$this, 'isAdmin']),
        ];
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return Session::isAdministrator();
    }
}
