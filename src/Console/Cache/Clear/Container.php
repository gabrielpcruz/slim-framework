<?php

namespace SlimFramework\Console\Cache\Clear;

use SlimFramework\Console\Console;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Container extends Console
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('slim:clear-cache-container');
        $this->setDescription('Clear the container cache.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cache = $this->getContainer()->get('settings')->get('application.path.cache');

        exec("rm -rf $cache/container/*");

        return Command::SUCCESS;
    }
}
