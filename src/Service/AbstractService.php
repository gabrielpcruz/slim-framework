<?php

namespace SlimFramework\Service;

use Illuminate\Database\Eloquent\Collection;
use SlimFramework\Entity\Entity;
use SlimFramework\Slim;
use SlimFramework\Repository\AbstractRepository;
use SlimFramework\Repository\RepositoryManager;
use DI\DependencyException;
use DI\NotFoundException;
use DomainException;
use Illuminate\Database\ConnectionInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;


abstract class AbstractService
{
    /**
     * @var RepositoryManager
     */
    private RepositoryManager $repositoryManager;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct()
    {
        $this->repositoryManager = Slim::container()->get(RepositoryManager::class);
    }

    /**
     * @param string|null $repositoryClass
     * @return AbstractRepository
     */
    final public function getRepository(string $repositoryClass = null): AbstractRepository
    {
        if (!$repositoryClass) {
            $repositoryClass = $this->getRepositoryClass();
        }

        return $this->repositoryManager->get($repositoryClass);
    }

    /**
     * @return string
     */
    protected function getRepositoryClass(): string
    {
        throw new DomainException(
            "Override the method 'getRepositoryClass' providing the corresponding class!"
        );
    }

    /**
     * @throws Throwable
     */
    final protected function beginTransaction(): void
    {
        $this->getConnection()->beginTransaction();
    }

    /**
     * @return ConnectionInterface
     */
    final protected function getConnection(): ConnectionInterface
    {
        return $this->repositoryManager->getConnection();
    }

    /**
     * @throws Throwable
     */
    final protected function rollBack(): void
    {
        $this->getConnection()->rollBack();
    }

    /**
     * @throws Throwable
     */
    final protected function commit(): void
    {
        $this->getConnection()->commit();
    }

    /**
     * @return RepositoryManager
     */
    final protected function getRepositoryManager(): RepositoryManager
    {
        return $this->repositoryManager;
    }

    /**
     * @return Collection|array
     */
    public function all(): Collection|array
    {
        return $this->getRepository()->all();
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function findById(int $id): ?Entity
    {
        return $this->getRepository()->findOneBy(['id' => $id]);
    }

    /**
     * @param Entity $entity
     * @return self
     */
    public function save(Entity $entity) : self
    {
        $this->getRepository()->save($entity);

        return $this;
    }

    /**
     * @param Entity $entity
     * @param array $attributes
     * @return self
     */
    public function update(Entity $entity, array $attributes) : self
    {
        $entity->fill($attributes);

        $this->getRepository()->save($entity);

        return $this;
    }
}
