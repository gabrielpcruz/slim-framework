<?php

namespace SlimFramework;

use Adbar\Dot;
use DomainException;
use Slim\Factory\AppFactory;
use Slim\Factory\Psr17\ServerRequestCreator;
use SlimFramework\Directory\Directory;
use SlimFramework\Handler\ErrorHandler;
use SlimFramework\Handler\HttpErrorHandler;
use SlimFramework\Provider\ProviderInterface;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App as SlimApp;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Flash\Messages;
use SlimFramework\Session\Session;

class Slim
{
    /**
     * @var string
     */
    public const string VERSION = '1.3.1';

    /**
     * @var string
     */
    public const string DEVELOPMENT = 'DEVELOPMENT';

    /**
     * @var string
     */
    public const string PRODUCTION = 'PRODUCTION';

    /**
     * @var string
     */
    public const string HOMOLOGATION = 'HOMOLOGATION';

    /**
     * @var SlimApp
     */
    private static SlimApp $app;

    /**
     * @var Container
     */
    private static Container $container;

    /**
     * @var Messages
     */
    private static Messages $flash;

    /**
     * @return bool
     */
    public static function isConsole(): bool
    {
        return self::getType() == 'console';
    }

    /**
     * @return string
     */
    private static function getType(): string
    {
        return php_sapi_name() == 'cli' ? 'console' : 'http';
    }

    /**
     * @return string
     */
    public static function getAppEnv(): string
    {
        return getenv('APP_ENV') ? strtoupper(getenv('APP_ENV')) : self::DEVELOPMENT;
    }

    /**
     * @return bool
     */
    public static function isDevelopment(): bool
    {
        return self::getAppEnv() == self::DEVELOPMENT;
    }

    /**
     * @return bool
     */
    public static function isHomologation(): bool
    {
        return self::getAppEnv() == self::HOMOLOGATION;
    }

    /**
     * @return bool
     */
    public static function isProduction(): bool
    {
        return self::getAppEnv() == self::PRODUCTION;
    }

    /**
     * @return string
     */
    public static function version(): string
    {
        if (self::isProduction()) {
            return Slim::VERSION;
        }

        return uniqid();
    }

    /**
     * @return Dot
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public static function settings(): Dot
    {
        $settings = 'settings';

        return self::container()->get($settings);
    }

    /**
     * @return ContainerInterface
     */
    public static function container(): ContainerInterface
    {
        if (!isset(self::$container)) {
            throw new DomainException('The container is not set!');
        }

        return self::$container;
    }

    /**
     * @return SlimApp
     */
    public static function getInstance(): SlimApp
    {
        if (!isset(self::$app)) {
            throw new DomainException('The App is not set!');
        }

        return self::$app;
    }

    /**
     * @return Messages
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function flash(): Messages
    {
        $flash = 'flash';

        if (!isset(self::$flash)) {
            self::$flash = self::container()->get($flash);
        }

        return self::$flash;
    }

    /**
     * @return SlimApp
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public static function bootstrap(): SlimApp
    {
        define('SLIM_FRAMEWORK_ROOT_PATH', str_replace('/src', '', __DIR__));
        $array = explode('/vendor', SLIM_FRAMEWORK_ROOT_PATH);
        define('SLIM_APPLICATION_ROOT_PATH', reset($array));

//        define('SLIM_FRAMEWORK_ROOT_PATH', str_replace('/src', '', __DIR__));
//        define('SLIM_APPLICATION_ROOT_PATH', str_replace('/framework/src', '', __DIR__));


        Session::start();

        self::$container = (new ContainerBuilder())->build();
        $app = AppFactory::createFromContainer(self::$container);
        self::$container->set(SlimApp::class, $app);
        self::$app = $app;

        (require self::settings()->get('application.path.config') . '/routes/web.php')($app);
        (require self::settings()->get('application.path.config') . '/routes/api.php')($app);


        self::defineConstants();
        self::cacheRoutes($app);

        self::provide();

        self::addErrorHandler($app);

        if (!Slim::isConsole()) {
            self::middlewares($app);
        }

        $settings = self::settings();

        error_reporting($settings->get('application.error.error_reporting'));
        ini_set('display_errors', $settings->get('application.error.display_errors'));
        ini_set('display_startup_errors', $settings->get('application.error.display_startup_errors'));

        // Timezone
        date_default_timezone_set($settings->get('application.timezone'));

        return $app;
    }

    /**
     * @param mixed $app
     * @return void
     */
    public static function cacheRoutes(mixed $app): void
    {
        $routeCollector = $app->getRouteCollector();
        $routeCollector->setCacheFile(STORAGE_PATH . '/cache/slim/routes.slim');
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    private static function provide(): void
    {
        $providersPath = self::settings()->get('slim_framework.path.provider');
        $providersNameSpace = "SlimFramework\\Provider\\";

        $providers = Directory::turnNameSpacePathIntoArray(
            $providersPath,
            $providersNameSpace,
            ['ProviderInterface.php']
        );

        /** @var ProviderInterface $provider */
        foreach ($providers as $provider) {
            $provider = new $provider();
            $provider->provide(self::container(), self::settings());
        }
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    private static function defineConstants(): void
    {
        $settings = self::settings();

        define('STORAGE_PATH', $settings->get('application.path.storage'));
        define('PUBLIC_PATH', $settings->get('application.path.public'));
    }

    /**
     * @param SlimApp $app
     * @return void
     */
    private static function middlewares(SlimApp $app): void
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public static function isGuestRoute(ServerRequestInterface $request): bool
    {
        $guestRoutes = Slim::settings()->get('system.guest_routes');

        if (in_array($request->getUri()->getPath(), $guestRoutes)) {
            return true;
        }

        return false;
    }

    /**
     * @param ServerRequestInterface $request
     * @param $route
     * @return bool
     */
    public static function isRouteEqualOf(ServerRequestInterface $request, $route): bool
    {
        if ($request->getUri()->getPath() === $route) {
            return true;
        }

        return false;
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public static function isRouteInMaintenance(ServerRequestInterface $request): bool
    {
        $routesInMaintenance = Slim::settings()->get('system.routes_in_maintenance');

        if (in_array($request->getUri()->getPath(), $routesInMaintenance)) {
            return true;
        }

        return false;
    }

    /**
     * @return ServerRequestInterface
     */
    public static function request(): ServerRequestInterface
    {
        $serverRequestCreator = ServerRequestCreatorFactory::create();
        return $serverRequestCreator->createServerRequestFromGlobals();
    }

    /**
     * @param SlimApp $app
     * @return void
     */
    public static function addErrorHandler(SlimApp $app): void
    {
        $request = self::request();

        $errorMiddleware = $app->addErrorMiddleware(true, true, true);

        $headerAccept = $request->getHeader('Accept');

        $isJsonApplication = count($headerAccept) === 1 && $headerAccept[0] === 'application/json';
        $isApiUri = str_contains($request->getUri()->getPath(), '/api');

        if ($isJsonApplication || $isApiUri) {
            $errorHandlerClass = new HttpErrorHandler(
                $app->getCallableResolver(),
                $app->getResponseFactory()
            );

            $errorMiddleware->setDefaultErrorHandler($errorHandlerClass);

            return;
        }

        $errorMiddleware->setDefaultErrorHandler(ErrorHandler::class);
    }
}
