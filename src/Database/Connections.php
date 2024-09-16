<?php

namespace App\src\Database;

use App\src\Slim;
use DomainException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Connections
{
    /**
     * @return array
     */
    public function getConnections(): array
    {
        $databaseConnections = [];

        try {
            $conections = (require_once Slim::settings()->get('file.database'));

            foreach ($conections as $connectionName => $connection) {
                $databaseConnections[$connectionName] = $connection;
            }

            if (!array_key_exists('default', $databaseConnections)) {
                if (empty($databaseConnections)) {
                    throw new DomainException('Improve an connections configuration!');
                }

                $databaseConnections['default'] = reset($databaseConnections);
            }
        } catch (Exception|NotFoundExceptionInterface|ContainerExceptionInterface $exception) {
            $databaseConnections = [];
        }

        return $databaseConnections;
    }
}
