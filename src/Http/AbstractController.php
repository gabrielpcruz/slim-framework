<?php

namespace SlimFramework\Http;

use SlimFramework\Slim;
use SlimFramework\Repository\RepositoryManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Flash\Messages;

abstract class AbstractController
{
    /**
     * @return RepositoryManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getRepositoryManager(): RepositoryManager
    {
        return Slim::container()->get(RepositoryManager::class);
    }

    /**
     * @return Messages
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function flash(): Messages
    {
        return Slim::flash();
    }
}
