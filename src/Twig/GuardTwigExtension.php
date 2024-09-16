<?php

namespace App\src\Twig;

use App\src\Session\Session;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GuardTwigExtension extends AbstractExtension
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'guard';
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('guard', [$this, 'guard']),
        ];
    }

    /**
     * @return bool
     */
    public function guard(): bool
    {
        return true === Session::isLoggedIn();
    }
}
