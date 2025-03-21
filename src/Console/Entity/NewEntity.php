<?php

namespace SlimFramework\Console\Entity;

use SlimFramework\Console\Console;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class NewEntity extends Console
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('slim:create-entity');
        $this->setDescription('Create a full new Entity (Entity/Repository).');
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
        $helper = $this->getHelper('question');

        $question = new Question(
            "Type the Entity name: ",
        );

        $entity = $helper->ask($input, $output, $question);


        $entityPath = $this->getContainer()->get('settings')->get('application.path.entity');

        $EntityName = ucfirst($entity);

        // Cria entity
        $newEntityPath = $EntityName;
        $command = "mkdir $entityPath/$newEntityPath";

        exec("$command");

        $newEntityName = $EntityName;
        $newEntity = "{$newEntityName}";

        $nameSpaceEntity = "SlimFramework\\Entity\\$EntityName";
        $newEntityClass = $this->templateEntity($newEntity, $nameSpaceEntity);


        sleep(1);

        $command = "echo '$newEntityClass' >> {$entityPath}/{$newEntityPath}/{$newEntityPath}Entity.php";
        exec("$command");

        // Cria Repository

        $repositoryPath = $this->getContainer()->get('settings')->get('application.path.repository');

        $newRepositoryPath = $EntityName;
        $command = "mkdir $repositoryPath/$newRepositoryPath";

        exec("$command");

        $newRepositoryName = $EntityName;
        $newRepository = "{$newRepositoryName}";

        $nameSpaceRepository = "SlimFramework\\Repository\\$EntityName";
        $newRepositoryClass = $this->templateRepository(
            $newRepository,
            "{$nameSpaceEntity}",
            $newEntity,
            $nameSpaceRepository
        );

        sleep(1);

        $class = "{$repositoryPath}/{$newRepositoryPath}/{$newRepositoryPath}Repository.php";

        $command = "echo '$newRepositoryClass' >> {$class}";
        exec("$command");

        return Command::SUCCESS;
    }

    private function templateEntity($entityName, $namespace): string
    {
        $tableEntity = strtolower($entityName);

        $entity = <<<STR
<?php

namespace $namespace;

class {$entityName}Entity extends Entity
{
    protected DOLAR_table = "$tableEntity";
}
STR;

        return str_replace("DOLAR_", "$", $entity);
    }

    private function templateRepository($repositoryName, $entityNameSpace, $entityName, $namespace): string
    {
        $entityFull = '\\' . $entityName . "Entity";
        return <<<STR
<?php

namespace $namespace;

{$entityNameSpace}{$entityFull};

class {$repositoryName}Repository extends Repository
{
    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return {$entityName}Entity::class;
    }
}

STR;
    }
}
