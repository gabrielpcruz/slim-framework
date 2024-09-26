<?php

namespace SlimFramework\Container;

use Adbar\Dot;
use SlimFramework\Configuration\ConfigurationInterface;
use SlimFramework\Repository\User\AccessTokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use SlimFramework\Service\Token\AuthorizationServer as SlimAuthorizationServer;
use Slim\App;
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
            'config' => function (ContainerInterface $container) {
                return $container->get('settings.config');
            },
            'settings' => function () {

                // Framework
                $configurationClasses = Directory::turnNameSpacePathIntoArray(
                    SLIM_FRAMEWORK_ROOT_PATH . '/src/Configuration',
                    'SlimFramework\\Configuration\\',
                    ['ConfigurationInterface.php']
                );

                $configurations = [];

                foreach ($configurationClasses as $class) {
                    /** @var ConfigurationInterface $configurationClass */
                    $configurationClass = new $class();

                    $configurations = array_replace_recursive($configurations, ($configurationClass)->configure());
                }

                // Application
                $configurationClasses = Directory::turnNameSpacePathIntoArray(
                    SLIM_APPLICATION_ROOT_PATH . '/app/Configuration',
                    'App\\Configuration\\'
                );

                foreach ($configurationClasses as $class) {
                    /** @var ConfigurationInterface $configurationClass */
                    $configurationClass = new $class();

                    $configurations = array_replace_recursive($configurations, ($configurationClass)->configure());
                }

                return new Dot($configurations);
            },

            Twig::class => function (ContainerInterface $container) {
                $settings = $container->get('settings');

                $rootPath = $settings->get('application.view.path');
                $templates = $settings->get('application.view.templates');
                $viewSettings = $settings->get('application.view.settings');
                $twigExtensionsPath = $settings->get('slim_framework.path.slim.twig_extensions');

                $loader = new FilesystemLoader([], $rootPath);

                foreach ($templates as $namespace => $template) {
                    $loader->addPath($template, $namespace);
                }

                $twig = new Twig($loader, $viewSettings);

                $extensions = Directory::turnNameSpacePathIntoArray(
                    $twigExtensionsPath,
                    "SlimFramework\\Twig\\"
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
            AuthorizationServer::class => factory([
                SlimAuthorizationServer::class,
                'create',
            ]),

            BearerTokenValidator::class => function (ContainerInterface $container) {
                $oauth2PublicKey = $container->get('settings')->get('file.oauth_public');

                /** @var RepositoryManager $repositoryManager */
                $repositoryManager = $container->get(RepositoryManager::class);

                /** @var AccessTokenRepository $accessTokenRepository */
                $accessTokenRepository = $repositoryManager->get(AccessTokenRepository::class);

                $beareValidator = new BearerTokenValidator($accessTokenRepository);
                $beareValidator->setPublicKey(new CryptKey($oauth2PublicKey));

                return $beareValidator;
            },
        ];
    }
}
