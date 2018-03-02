<?php
//phpinfo();exit;
//include_once __DIR__ . '/../app/Autoloader.php';
//new \app\Autoloader(__DIR__ . '/../');
include_once __DIR__ . '/../vendor/autoload.php';
$config = include_once __DIR__ . '/../config/config.php';
$app = new \app\Application($config);
include_once __DIR__ . '/../config/routes.php';
$app->run();
