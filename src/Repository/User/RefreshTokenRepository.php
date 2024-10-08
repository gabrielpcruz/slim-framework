<?php

/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace SlimFramework\Repository\User;

use SlimFramework\Entity\User\AccessTokenEntity;
use SlimFramework\Entity\User\RefreshTokenEntity;
use Exception;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use SlimFramework\Repository\AbstractRepository;

class RefreshTokenRepository extends AbstractRepository implements RefreshTokenRepositoryInterface
{
    use RefreshTokenTrait;

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return RefreshTokenEntity::class;
    }

    /**
     * @param RefreshTokenEntityInterface $refreshTokenEntity
     * @return void
     * @throws Exception
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        /** @var AccessTokenEntity $accessToken */
        $accessToken = $refreshTokenEntity->getAccessToken();

        /** @var RefreshTokenEntity $refreshTokenEntity */
        $refreshTokenEntity->setAttribute('oauth2_access_token_id', $accessToken->getAttribute('id'));
        $refreshTokenEntity->setAttribute('refresh_token', $refreshTokenEntity->getIdentifier());
        $refreshTokenEntity->setAttribute('expire_time', $refreshTokenEntity->getExpiryDateTime()->add(datePeriod(0, 1)));


        $this->save($refreshTokenEntity);
    }

    /**
     * @param $tokenId
     * @return void
     */
    public function revokeRefreshToken($tokenId)
    {
        // Some logic to revoke the refresh token in a database
    }

    /**
     * @param $tokenId
     * @return bool
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        /** @var AccessTokenEntity $token */
        $token = $this->findOneBy([
            'refresh_token' => $tokenId,
        ]);

        return !$token || ($token->isExpired());
    }

    /**
     * @return RefreshTokenEntity
     */
    public function getNewRefreshToken(): RefreshTokenEntity
    {
        return new RefreshTokenEntity();
    }
}
