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

        // Slim Framework Console
        $commands = $this->slimFrameworkConsole($excludeClasses, $excludePaths, $commands);

        $appConsoleNamespace = "App\\Console\\";
        $consolePath = Slim::settings()->get('application.path.console');

        $appConsole = Directory::turnNameSpacePathIntoArray(
            $consolePath,
            $appConsoleNamespace,
            $excludeClasses
        );

        return array_merge($commands, $appConsole);
    }

    /**
     * @return string[]
     */
    private function getExcludedClasses(): array
    {
        return [
            "ConsoleMigration.php",
            "ConsoleMapper.php",
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

        ];
    }

    /**
     * @param array $excludeClasses
     * @param array $excludePaths
     * @param array $commands
     * @return array
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    private function slimFrameworkConsole(array $excludeClasses, array $excludePaths, array $commands): array
    {
        $consoleNamespace = "SlimFramework\\Console\\";
        $consolePath = Slim::settings()->get('slim_framework.path.console');

        $consoleCommands = Directory::turnNameSpacePathIntoArray(
            $consolePath,
            $consoleNamespace,
            $excludeClasses,
            $excludePaths
        );

        return array_merge($commands, $consoleCommands);
    }
}
