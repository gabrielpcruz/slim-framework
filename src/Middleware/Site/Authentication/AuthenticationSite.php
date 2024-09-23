<?php

namespace SlimFramework\Middleware\Site\Authentication;

use Exception;
use SlimFramework\Slim;
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

class AuthenticationSite extends MiddlewareSite
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (
            !Slim::settings()->get('system.maintenance') &&
            !Session::isLoggedIn() &&
            !Slim::isGuestRoute($request)
        ) {
            flash()->addMessage(FlashMessage::ERROR, MessageExceptionSystem::MES0001);

            return redirect('/login');
        }

        return $handler->handle($request);
    }
}
