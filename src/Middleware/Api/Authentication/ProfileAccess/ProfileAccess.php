<?php

namespace SlimFramework\Middleware\Api\Authentication\ProfileAccess;

use SlimFramework\Slim;
use SlimFramework\Entity\User\UserEntity;
use SlimFramework\Repository\User\UserRepository;
use SlimFramework\Exception\UserNotAllowedException;
use SlimFramework\Middleware\Api\MiddlewareApi;
use DI\DependencyException;
use DI\NotFoundException;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;
use SlimFramework\Repository\RepositoryManager;

abstract class ProfileAccess extends MiddlewareApi implements ProfileAccessInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return UserEntity
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws OAuthServerException
     */
    protected function getUser(ServerRequestInterface $request): UserEntity
    {
        $repositoryManager = App::container()->get(RepositoryManager::class);

        /** @var BearerTokenValidator $bearerValidator */
        $bearerValidator = App::container()->get(BearerTokenValidator::class);

        /** @var UserRepository $userRepository */
        $userRepository = $repositoryManager->get(UserRepository::class);

        $request = $bearerValidator->validateAuthorization($request);

        /** @var UserEntity $user */
        $user = $userRepository->findOneBy(
            ['client_id' => $request->getAttribute('oauth_client_id')]
        );

        return $user;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ReflectionException
     * @throws UserNotAllowedException
     */
    public function handle(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->allowed($request)) {
            return $handler->handle($request);
        }

        throw new UserNotAllowedException();
    }
}
