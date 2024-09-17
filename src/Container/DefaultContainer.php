<?php

namespace SlimFramework\Container;

use SlimFramework\Repository\User\AccessTokenAbstractRepository;
use SlimFramework\Service\Token\AuthorizationServer;
use SlimFramework\Service\Token\AuthorizationServer as SlimAuthorizationServer;
use SlimFramework\Slim;
use SlimFramework\Directory\Directory;
use SlimFramework\Repository\RepositoryManager;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\ConnectionInterface;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\CryptKey;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;
use function DI\autowire;
use function DI\factory;

class DefaultContainer implements SlimContainerApp
{
    /**
     * @return array
     */
    public function getDefinitions(): array
    {
        return [
            Slim::class => function (ContainerInterface $container) {
                $app = AppFactory::createFromContainer($container);

                // Adding routes of application
                (require __DIR__ . '/../routes/web.php')($app);
                (require __DIR__ . '/../routes/api.php')($app);

                $app->addRoutingMiddleware();

                return $app;
            },

            Twig::class => function (ContainerInterface $container) {
                $settings = $container->get('settings');

                $rootPath = $settings->get('view.path');
                $templates = $settings->get('view.templates');
                $viewSettings = $settings->get('view.settings');
                $twigExtensionsPath = $settings->get('path.slim.twig');

                $loader = new FilesystemLoader([], $rootPath);

                foreach ($templates as $namespace => $template) {
                    $loader->addPath($template, $namespace);
                }

                $twig = new Twig($loader, $viewSettings);

                $extensions = Directory::turnNameSpacePathIntoArray(
                    $twigExtensionsPath,
                    "\\SlimFramework\\Slim\\Twig\\"
                );

                $twig->addExtension(new DebugExtension());
                $twig->addExtension(new IntlExtension());

                foreach ($extensions as $extension) {
                    $twig->addExtension(new $extension());
                }

                return $twig;
            },

            RepositoryManager::class => autowire(),

            ConnectionInterface::class => function (ContainerInterface $container) {
                return Manager::connection('default');
            },

            // OAuth
            SlimAuthorizationServer::class => factory([
                SlimAuthorizationServer::class,
                'create',
            ]),

            BearerTokenValidator::class => function (ContainerInterface $container) {
                $oauth2PublicKey = $container->get('settings')->get('file.oauth_public');

                /** @var RepositoryManager $repositoryManager */
                $repositoryManager = $container->get(RepositoryManager::class);

                /** @var AccessTokenAbstractRepository $accessTokenRepository */
                $accessTokenRepository = $repositoryManager->get(AccessTokenAbstractRepository::class);

                $beareValidator = new BearerTokenValidator($accessTokenRepository);
                $beareValidator->setPublicKey(new CryptKey($oauth2PublicKey));

                return $beareValidator;
            },
        ];
    }
}
