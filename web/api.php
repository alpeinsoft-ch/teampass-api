<?php

ini_set('display_errors', 0);

require_once __DIR__.'/../vendor/autoload.php';

use Teampass\Api\Application;

/**
 * @var Application $app
 */
$app = new Application();
$app->mountControllers();

$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
$port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : null;
if (!in_array($port, ['80', '443']) && (false === $pos = strrpos($host, ':'))) {
    $_SERVER['HTTP_HOST'] .= ':'.$port;
}

$app->run();
