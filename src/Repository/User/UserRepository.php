<?php

/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace SlimFramework\Repository\User;

use SlimFramework\Entity\User\ClientEntity;
use SlimFramework\Entity\User\UserEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use SlimFramework\Repository\AbstractRepository;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return UserEntity::class;
    }

    /**
     * @param $username
     * @param $password
     * @param $grantType
     * @param ClientEntityInterface $clientEntity
     * @return false|UserEntityInterface|mixed|null
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ): mixed {
        $queryBuilder = $this->query();

        $queryBuilder->where('username', '=', $username);
        $user = $queryBuilder->get()->first();

        if (!password_verify($password, $user->password)) {
            return false;
        }

        /** @var ClientEntity $clientEntity */
        if ($user->client_id != $clientEntity->id) {
            return false;
        }

        return $user;
    }

    /**
     * @param array $data
     *
     * @return false|UserEntityInterface
     */
    public function getUserEntityByCredentials(array $data): false|UserEntityInterface
    {
        $queryBuilder = $this->query();

        $queryBuilder->where('username', '=', $data['username']);
        $user = $queryBuilder->get()->first();

        if (!password_verify($data['password'], $user->password)) {
            return false;
        }

        return $user;
    }

    /**
     * @param array $data
     *
     * @return null|false|mixed|UserEntityInterface
     */
    public function getUserEntityByClientIdentifier(array $data): mixed
    {
        $queryBuilder = $this->query();

        $queryBuilder->where('username', '=', $data['username']);
        $user = $queryBuilder->get()->first();

        if (!password_verify($data['password'], $user->password)) {
            return false;
        }

        return $user;
    }

    /**
     * @param array $data
     *
     * @return null|false|mixed|UserEntityInterface
     */
    public function getUserByToken(array $data): mixed
    {
        $queryBuilder = $this->query();

        $queryBuilder->join('oauth2_access_token', 'id', '=', 'user_id');

        $queryBuilder->where('access_token', '=', $data['token']);

        return $queryBuilder->get()->first();
    }
}
