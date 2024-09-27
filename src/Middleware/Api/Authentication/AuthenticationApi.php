<?php

namespace SlimFramework\Middleware\Api\Authentication;

use SlimFramework\Slim;
use SlimFramework\Entity\User\ClientEntity;
use SlimFramework\Repository\User\AccessTokenRepository;
use SlimFramework\Repository\User\ClientRepository;
use SlimFramework\Middleware\Api\MiddlewareApi;
use DI\DependencyException;
use DI\NotFoundException;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;
use SlimFramework\Exception\HttpUnauthorizedException;
use SlimFramework\Repository\RepositoryManager;

class AuthenticationApi extends MiddlewareApi
{
    /**
     * @var RepositoryManager
     */
    protected RepositoryManager $repositoryManager;

    /**
     * @param RepositoryManager $repositoryManager
     */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     * @throws HttpUnauthorizedException
     */
    public function authenticate(ServerRequestInterface $request): ServerRequestInterface
    {
        /** @var string $oauth2PublicKey */
        $oauth2PublicKey = Slim::settings()->get('application.file.oauth_public');

        /** @var AccessTokenRepository $accessTokenRepository */
        $accessTokenRepository = $this->repositoryManager->get(AccessTokenRepository::class);

        $server = new ResourceServer(
            $accessTokenRepository,
            $oauth2PublicKey
        );

        $request = $server->validateAuthenticatedRequest($request);
        $clientRepository = $this->repositoryManager->get(ClientRepository::class);

        /** @var ClientEntity $client */
        $client = $clientRepository->findOneBy([
            'id' => $request->getAttribute('oauth_client_id'),
        ]);

        if (!$client) {
            throw new HttpUnauthorizedException($request);
        }

        return $request->withAttribute('oauth_client_id', $client->getAttribute('id'));
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws HttpUnauthorizedException
     * @throws ReflectionException
     */
    public function handle(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $this->authenticate($request);

        return $handler->handle($request);
    }
}
