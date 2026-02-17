<?php

namespace SlimFramework;

use SlimFramework\Container\DefaultContainer;
use SlimFramework\Directory\Directory;
use DI\Container;
use DI\ContainerBuilder as ContainerBuilderDI;
use Exception;
use Slim\Flash\Messages;
use function DI\autowire;

final class ContainerBuilder
{
    /**
     * @return Container
     * @throws Exception
     */
    public function build(): Container
    {
        $definitions = (new DefaultContainer())->getDefinitions();

        $settings = $definitions['settings']();

        $container = (new ContainerBuilderDI());
        $container->addDefinitions($definitions);
        $container->addDefinitions([
            'flash' => function () {
                return new Messages($_SESSION);
            }
        ]);

        $container->addDefinitions($this->getDefinitions());
        
        if ($settings->get('application.cache.container')) {
            $container->enableCompilation(SLIM_APPLICATION_ROOT_PATH . '/storage/cache/container');
            $container->writeProxiesToFile(true, SLIM_APPLICATION_ROOT_PATH . '/storage/cache/proxy');
        }

        return $container->build();
    }

    /**
     * @return array
     */
    private function getDefinitions(): array
    {
        $definitions = [];

        $controllers = Directory::turnNameSpacePathIntoArray(
            SLIM_APPLICATION_ROOT_PATH . '/app/Http',
            "App\\Http\\",
            [
                "AbstractController.php",
                "SiteAbstractController.php",
                "ApiAbstractController.php"
            ]
        );

        foreach ($controllers as $controller) {
            $definitions[$controller] = autowire();
        }

        return $definitions;
    }
}
