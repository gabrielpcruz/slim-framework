<?php

namespace SlimFramework\Configuration;

use PHPMailer\PHPMailer\SMTP;

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
                    'twig' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Twig',
                ],
                'provider' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Provider',
                'repository' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Repository',
                'entity' => SLIM_FRAMEWORK_ROOT_PATH . '/src/Entity',
            ]
        ];

        $configuration['slim_application'] = [
            'path' => [
                'tests' => SLIM_APPLICATION_ROOT_PATH . '/tests',
                'public' => SLIM_APPLICATION_ROOT_PATH . '/public',
                'assets' => 'assets/',
                'config' => SLIM_APPLICATION_ROOT_PATH . '/config',
                'data' => SLIM_APPLICATION_ROOT_PATH . '/data',
                'storage' => SLIM_APPLICATION_ROOT_PATH . '/storage',
                'cache' => SLIM_APPLICATION_ROOT_PATH . '/storage/cache',
                'database' => SLIM_APPLICATION_ROOT_PATH . '/config/database',
                'console' => SLIM_APPLICATION_ROOT_PATH . '/src/Console',
                'migration' => SLIM_APPLICATION_ROOT_PATH . '/src/Migration',
                'seeder' => SLIM_APPLICATION_ROOT_PATH . '/src/Seeder',
                'slim' => [
                    'console' => [
                        'cache' => SLIM_APPLICATION_ROOT_PATH . '/app/Console/Cache',
                        'database' => SLIM_APPLICATION_ROOT_PATH . '/app/Console/Database',
                        'entity' => SLIM_APPLICATION_ROOT_PATH . '/app/Console/Entity',
                        'migration' => SLIM_APPLICATION_ROOT_PATH . '/app/Console/Migration',
                        'seeder' => SLIM_APPLICATION_ROOT_PATH . '/app/Console/Seeder',
                    ],
                    'migration' => SLIM_APPLICATION_ROOT_PATH . '/app/Migration',
                    'seeder' => SLIM_APPLICATION_ROOT_PATH . '/app/Seeder',
                    'twig' => SLIM_APPLICATION_ROOT_PATH . '/app/Twig',
                ],
                'provider' => SLIM_APPLICATION_ROOT_PATH . '/app/Provider',
                'repository' => SLIM_APPLICATION_ROOT_PATH . '/app/Repository',
                'entity' => SLIM_APPLICATION_ROOT_PATH . '/app/Entity',
                'files' => [
                    'images' => SLIM_APPLICATION_ROOT_PATH . '/storage/images'
                ]
            ]
        ];

        $configuration['slim_application']['system'] = [
            'maintenance' => 0,
            'maintenance_return' => '2023-07-16 12:07',
            'maintenance_route' => '/maintenance',
            'guest_routes' => [
                '/login',
            ],
            'routes_in_maintenance' => [
            ],
        ];


        $configuration['slim_application']['file'] = [
            'database' => $configuration['slim_application']['path']['config'] . '/database.php',
            'oauth_private' => $configuration['slim_application']['path']['data'] . '/oauth/keys/private.key',
            'oauth_public' => $configuration['slim_application']['path']['data'] . '/oauth/keys/public.key',
        ];

        $configuration['slim_application']['mailer'] = [
            //PHPMailer settings
            'phpmailer' => [
                //Configs
                'smtp_host' => 'smtp.example.com',
                'smtp_debug' => SMTP::DEBUG_OFF,
                'smtp_exceptions' => false,

                'smtp_port' => 465,
                'smtp_options' => [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ],

                // Auth
                'username' => 'youremail@gmail.com',
                'password' => 'yourpasswordemail',
            ]
        ];

        $configuration['slim_application']['error'] = [
            'slashtrace' => 1, // Exibir erros na tela
            'error_reporting' => 1,
            'display_errors' => 1,
            'display_startup_errors' => 1,
        ];


        $configuration['slim_application']['timezone'] = 'America/Sao_Paulo';

        $configuration['slim_application']['view'] = [
            'path' => SLIM_FRAMEWORK_ROOT_PATH . '/resources/views',

            'templates' => [
                'api' => SLIM_FRAMEWORK_ROOT_PATH . '/resources/views/api',
                'console' => SLIM_FRAMEWORK_ROOT_PATH . '/resources/views/console',
                'email' => SLIM_FRAMEWORK_ROOT_PATH . '/resources/views/email',
                'error' => SLIM_FRAMEWORK_ROOT_PATH . '/resources/views/error',
                'layout' => SLIM_FRAMEWORK_ROOT_PATH . '/resources/views/layout',
                'site' => SLIM_FRAMEWORK_ROOT_PATH . '/resources/views/site',
            ],

            'settings' => [
                'cache' => SLIM_FRAMEWORK_ROOT_PATH . '/storage/cache/views',
                'debug' => true,
                'auto_reload' => true,
            ],

            'assets' => [
                // Public assets cache directory
                'path' => SLIM_FRAMEWORK_ROOT_PATH . '/public/assets',

                // Public url base path
                'url_base_path' => SLIM_FRAMEWORK_ROOT_PATH . '/public/assets',

                // Internal cache directory for the assets
                'cache_path' => SLIM_FRAMEWORK_ROOT_PATH . '/storage/cache/views',

                'cache_name' => 'assets-cache',

                //  Should be set to 1 (enabled) in production
                'minify' => 1,
            ]
        ];

        return $configuration;
    }
}