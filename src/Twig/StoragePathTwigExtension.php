<?php

namespace SlimFramework\Twig;

use SlimFramework\Slim;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StoragePathTwigExtension extends AbstractExtension
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'storage_path';
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('storage_path', [$this, 'storagePath']),
        ];
    }

    /**
     * @return string
     */
    public function storagePath(): string
    {
        $host = $_SERVER['HTTP_HOST'];

        $protocol = Slim::isProduction() ? 'https' : 'http';

        return "{$protocol}://{$host}/storage";
    }
}
