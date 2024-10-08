<?php

namespace SlimFramework\Console\Seeder;

use SlimFramework\Slim;
use SlimFramework\Console\Console;
use SlimFramework\Directory\Directory;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Seed extends Console
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('slim:seed');
        $this->setDescription('Run the seeders commands presents on "SlimFramework\\Seeder" namespace.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $seederPath = Slim::settings()->get('application.path.seeder');

        $seederPath = Directory::turnNameSpacePathIntoArray(
            $seederPath,
            "\\App\\Seeder\\",
            [],
            ['Slim']
        );

        $seeders = Directory::getIterator($seederPath);

        foreach ($seeders as $seeder) {
            (new $seeder())->run();
        }

        return Command::SUCCESS;
    }
}
