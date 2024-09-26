<?php

namespace SlimFramework\Middleware\Site\Maintenance;

use Exception;
use SlimFramework\Slim;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SlimFramework\Middleware\Middleware;

class RoutesInMaintenanceMiddleware extends Middleware
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (Slim::isRouteInMaintenance($request)) {
            return redirect('/route_maintenance');
        }

        return $handler->handle($request);
    }
}
