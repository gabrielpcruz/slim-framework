<?php

namespace SlimFramework\Console;

use SlimFramework\Slim;
use SlimFramework\Directory\Directory;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ConsoleMapper
{

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getCommands(): array
    {
        $commands = [];

        $excludeClasses = $this->getExcludedClasses();

        $excludePaths = $this->getExcludedPaths();

        // Seeder
        $seederNamespace = "App\\Seeder\\";
        $seederPath = Slim::settings()->get('application.path.slim.seeder');

        $seederCommands = Directory::turnNameSpacePathIntoArray(
            $seederPath,
            $seederNamespace,
            $excludeClasses
        );

        // Console
        $consoleCommands = [];
        $consoleNamespace = "App\\Console\\";
        $consolePath = Slim::settings()->get('application.path.console');

        $consoleCommands = Directory::turnNameSpacePathIntoArray(
            $consolePath,
            $consoleNamespace,
            $excludeClasses,
            $excludePaths
        );

        $commands = array_merge($commands, $seederCommands);
        $commands = array_merge($commands, $consoleCommands);

        // Slim
        $slimPaths = Slim::settings()->get('application.path.slim.console');

        foreach ($slimPaths as $slimPath) {
            $namespace = Directory::turnPathIntoNameSpace($slimPath);
            $arr = Directory::turnNameSpacePathIntoArray(
                $slimPath,
                $namespace,
                $excludeClasses,
                $excludePaths
            );

            $commands = array_merge($commands, $arr);
        }

        return $commands;
    }

    /**
     * @return string[]
     */
    private function getExcludedClasses(): array
    {
        return [
            "ConsoleMigration.php",
            "Console.php",
            "MigrationTrait.php",
            "Migration.php",
            "SeederInterface.php",
            "AbstractSeeder.php",
        ];
    }

    /**
     * @return string[]
     */
    private function getExcludedPaths(): array
    {
        return [
            'Migration',
            'Slim'
        ];
    }
}
