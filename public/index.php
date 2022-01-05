<?php

use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Url;
use Phalcon\Mvc\Application;
use Phalcon\Db\Adapter\Pdo\Mysql;

use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;

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

require '../config/config.php';

$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ]
);

$loader->register();

$token = $config['influxDB']['api_key'];
$org = "zikisacalesogaxa@gmail.com";
$bucket = "zikisacalesogaxa's Bucket";

$client = new Client([
    "url" => "https://us-east-1-1.aws.cloud2.influxdata.com",
    "token" => $token,
]);

$writeApi = $client->createWriteApi();

$data = "mem,host=host1 used_percent=31.23";

$writeApi->write($data, WritePrecision::S, $bucket, $org);

$query = "from(bucket: \"zikisacalesogaxa's Bucket\") |> range(start: -1h)";
$tables = $client->createQueryApi()->query($query, $org);

foreach ($tables as $table) {
    foreach ($table->records as $record) {
        $time = $record->getTime();
        $measurement = $record->getMeasurement();
        $field = $record->getField();
        $value = $record->getValue();
    }
}

$client->close();

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
    function() use ($config) {
        return new Mysql(
            $config['database']
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
