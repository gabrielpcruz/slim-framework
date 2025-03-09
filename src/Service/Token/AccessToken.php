<?php

namespace SlimFramework\Service\Token;

use DomainException;
use SlimFramework\Entity\User\ClientEntity;
use SlimFramework\Repository\User\AccessTokenRepository;
use SlimFramework\Repository\User\ClientRepository;
use SlimFramework\Repository\User\UserRepository;
use SlimFramework\Service\AbstractService;


class AccessToken extends AbstractService
{
    /**
     * @return string
     */
    protected function getRepositoryClass(): string
    {
        return AccessTokenRepository::class;
    }

    /**
     * @param array $data
     *
     * @return ClientEntity|null
     */
    public function getClientByGrant(array $data): ?ClientEntity
    {
        $grant_type = $data['grant_type'];

        return match ($grant_type) {
            'refresh_token' => $this->getClientByIdentifier($data),
            default => $this->getClientByEmailPassword($data),
        };
    }

    /**
     * @param array $data
     *
     * @return object
     */
    private function getClientByEmailPassword(array $data): object
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getRepository(UserRepository::class);

        $user = $userRepository->getUserEntityByCredentials($data);

        if (!$user) {
            throw new DomainException('Usuário não encontrado');
        }

        $client = new ClientEntity();
        $client->setAttribute('id', $user->client->getAttribute('id'));
        $client->setAttribute('identifier', $user->client->getAttribute('identifier'));

        return $client;
    }

    /**
     * @param array $data
     *
     * @return ClientEntity|null
     */
    private function getClientByIdentifier(array $data): ?ClientEntity
    {
        /** @var ClientRepository $clientRepository */
        $clientRepository = $this->getRepository(ClientRepository::class);

        return $clientRepository->getClientEntityByCredentials(
            $data
        );
    }
}
