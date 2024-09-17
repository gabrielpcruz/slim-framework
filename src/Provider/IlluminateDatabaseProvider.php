<?php

namespace SlimFramework\Provider;

use Adbar\Dot;
use SlimFramework\Database\Connections;
use Illuminate\Database\Capsule\Manager;
use Psr\Container\ContainerInterface;

class IlluminateDatabaseProvider implements ProviderInterface
{
    /**
     * @param ContainerInterface $container
     * @param Dot $settings
     * @return void
     */
    public function provide(ContainerInterface $container, Dot $settings): void
    {
        $manager = new Manager();

        $conections = (new Connections())->getConnections();

        foreach ($conections as $name => $conection) {
            $manager->addConnection($conection, $name);
        }

        $manager->setAsGlobal();
        $manager->bootEloquent();
    }
}
