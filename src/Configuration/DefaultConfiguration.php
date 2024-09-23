<?php

namespace SlimFramework\Configuration;

class DefaultConfiguration implements ConfigurationInterface
{
    public function configure(): array
    {
        $rootCli = str_replace('/src/Configuration/DefaultConfiguration.php', '', __FILE__);

        if (!defined('SLIM_FRAMEWORK_ROOT_PATH')) {
            define('SLIM_FRAMEWORK_ROOT_PATH', $rootCli);
        }

        $configuration = [];

        $configuration['settings'] = [];

        $configuration['root'] = SLIM_FRAMEWORK_ROOT_PATH;

        $configuration['slim_framework'] = [
            'path' => [
                'tests' => SLIM_FRAMEWORK_ROOT_PATH . '/tests',
                'config' => SLIM_FRAMEWORK_ROOT_PATH . '/config',
                'console' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Console',
                'migration' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Migration',
                'seeder' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Seeder',
                'slim' => [
                    'console' => [
                        'cache' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Console/Cache',
                        'database' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Console/Database',
                        'entity' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Console/Entity',
                        'migration' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Console/Migration',
                        'oauth' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Console/Oauth',
                        'seeder' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Console/Seeder',
                    ],
                    'migration' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Migration',
                    'seeder' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Seeder',
                    'twig_extensions' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Twig',
                ],
                'provider' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Provider',
                'repository' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Repository',
                'entity' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Entity',
            ]
        ];

        return $configuration;
    }
}