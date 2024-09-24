<?php

namespace App\Middleware\Site\Maintenance;

use SlimFramework\Slim;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SlimFramework\Middleware\Site\MiddlewareSite;

class MaintenanceMiddleware extends MiddlewareSite
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function handle(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $isSystemInMaintenance = Slim::settings()->get('application.system.maintenance');
        $isRouteMaintenance = Slim::isRouteEqualOf($request, '/maintenance');
        $isRouteLogin = Slim::isRouteEqualOf($request, '/login');

        if ($isSystemInMaintenance && (!$isRouteMaintenance || $isRouteLogin)) {
            return redirect('/maintenance');
        }

        if (!$isSystemInMaintenance && $isRouteMaintenance) {
            return redirect('/login');
        }

        return $handler->handle($request);
    }
}
