<?php

namespace App\src\Seeder;

use App\src\Slim;
use DI\DependencyException;
use DI\NotFoundException;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Seeder as IlluminateSeeder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class AbstractSeeder extends IlluminateSeeder implements SeederInterface
{
    /**
     * @return ConnectionInterface
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    protected function connection(): ConnectionInterface
    {
        return Slim::container()->get(ConnectionInterface::class);
    }
}
