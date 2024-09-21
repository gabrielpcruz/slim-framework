<?php

namespace SlimFramework\Database;

use SlimFramework\Slim;
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
            $hasDatabaseConfiguration = Slim::settings()->has('application.file.database');

            if (!$hasDatabaseConfiguration) {
                return $databaseConnections;
            }

            $conections = (require_once Slim::settings()->get('application.file.database'));

            if (!is_array($conections)) {
                return $databaseConnections;
            }

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
