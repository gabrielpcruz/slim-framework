<?php

namespace SlimFramework\Console\Migration;

use SlimFramework\Migration\ConsoleMigration;
use SlimFramework\Migration\Migration;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends ConsoleMigration
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('slim:migration-migrate');
        $this->setDescription('Run all migrations on database presents on "SlimFramework\\Migration" namespace.');
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
        try {
            $this->comment("Creating tables...");

            /** @var Migration $migration */
            foreach ($this->migrations() as $migration) {
                $this->info("table {$this->getTableName($migration)}...");

                $migration->up();
            }
        } catch (Exception $exception) {
            $this->error($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
