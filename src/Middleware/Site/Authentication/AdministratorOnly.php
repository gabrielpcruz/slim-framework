<?php

namespace SlimFramework\Middleware\Site\Authentication;

use Exception;
use SlimFramework\Enum\EnumProfile;
use SlimFramework\Enum\FlashMessage;
use SlimFramework\Message\Exception\System\MessageExceptionSystem;
use SlimFramework\Session\Session;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SlimFramework\Middleware\Site\MiddlewareSite;

class AdministratorOnly extends MiddlewareSite
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->isAdministrator()) {
            Session::logout();

            return redirect('/login');
        }

        return $handler->handle($request);
    }

    /**
     * @return bool
     */
    private function isAdministrator(): bool
    {
        if (!Session::isLoggedIn()) {
            return false;
        }

        $usuario = Session::getUser();

        if (!$usuario) {
            return false;
        }

        if (!EnumProfile::isAdmin($usuario->profile()->first()->name)) {
            flash()->addMessage(FlashMessage::ERROR, MessageExceptionSystem::MES0001);
            return false;
        }

        return EnumProfile::isAdmin($usuario->profile()->first()->name);
    }
}
