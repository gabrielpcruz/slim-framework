<?php

/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace SlimFramework\Repository\User;

use SlimFramework\Entity\User\ScopeEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use SlimFramework\Repository\AbstractRepository;
use function array_key_exists;

class ScopeRepository extends AbstractRepository implements ScopeRepositoryInterface
{
    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return ScopeEntity::class;
    }

    /**
     * @param $identifier
     * @return ScopeEntity|void
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        $scopes = [
            'basic' => [
                'description' => 'Basic details about you',
            ],
            'email' => [
                'description' => 'Your email address',
            ],
        ];
        if (array_key_exists($identifier, $scopes) === false) {
            return;
        }

        $scope = new ScopeEntity();
        $scope->setIdentifier($identifier);
        return $scope;
    }

    /**
     * @param array $scopes
     * @param $grantType
     * @param ClientEntityInterface $clientEntity
     * @param $userIdentifier
     * @return array|ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ): array {
        // Example of programatically modifying the final scope of the access token
        if ((int)$userIdentifier === 1) {
            $scope = new ScopeEntity();
            $scope->setIdentifier('email');
            $scopes[] = $scope;
        }

        return $scopes;
    }
}
