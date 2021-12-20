<?php

use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Url;
use Phalcon\Mvc\Application;
use Phalcon\Db\Adapter\Pdo\Mysql;

define('PHALCON_BASE_PATH', dirname(__DIR__));
define('APP_PATH', PHALCON_BASE_PATH . '/app');
define('PHALCON_VENDOR_PATH', PHALCON_BASE_PATH.'/vendor');

// Load Composer's autoloader
require_once PHALCON_VENDOR_PATH . '/autoload.php';
// ...
// Load dotenv?
if (class_exists('Dotenv\Dotenv') && file_exists(PHALCON_BASE_PATH . '/.env')) {
    (Dotenv\Dotenv::create(PHALCON_BASE_PATH))->load();
}

$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ]
);

$loader->register();

// Create a DI
$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');

        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');

        return $url;
    }
);

$container->set(
    'db',
    function() {
        return new Mysql(
            [
                'host'      =>  getenv('DB_HOST'),
                'port'      =>  getenv('DB_PORT'),
                'username'  =>  getenv('DB_USERNAME'),
                'password'  =>  getenv('DB_PASSWORD'),
                'dbname'    =>  getenv('DB_DATABASE')
            ]
        );
    }
);

$application = new Application($container);

try {
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exeption $e) {
    echo 'Exception: ', $e->getMessage();
}
