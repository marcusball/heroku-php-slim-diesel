<?php

use Psr\Container\ContainerInterface;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },

    ErrorMiddleware::class => function (ContainerInterface $container) {
        $app = $container->get(App::class);
        $settings = $container->get('settings')['error'];

        return new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details']
        );
    },

    PDO::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];

        $driver = $settings['driver'];
        $host = $settings['host'];
        $port = $settings['port'];
        $dbname = $settings['database'];
        $username = $settings['username'];
        $password = $settings['password'];
        $query = $settings['query'];
        $charset = $settings['charset'];
        $flags = $settings['flags'];

        $charsetStr = '';
        if ($driver === 'pgsql') { $charsetStr = "options='--client_encoding=$charset'"; }
        else if ($driver === 'mysql') { $charsetStr = "charset=$charset"; }

        // Create an array of all of the parts that will be included in the final DSN string. 
        $dsnParts = [
            "$driver:host=$host",
            "dbname=$dbname",
        ];

        // Add DB port if it was specified
        if (!is_null($port)) {
            $dsnParts[] = "port=$port";
        }

        // If query parameters are set, such as "host/db?sslmode=require", then add them to the DSN set. 
        if (!is_null($query)) {
            $queryArgs = explode('&', $query);
            array_merge($dsnParts, $queryArgs);
        }

        $dsnParts[] = $charsetStr;

        // Construct final DSN string and create connection
        $dsn = implode(';', $dsnParts);
        return new PDO($dsn, $username, $password, $flags);
    },

    Monolog\Logger::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['logger'];

        $logger = new Logger($settings['name']);
        $streamHandler = new StreamHandler('php://stderr', 100);
        $logger->pushHandler($streamHandler);

        return $logger;
    }
];