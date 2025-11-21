<?php

$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'test';
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? '1';
$_ENV['APP_ENV'] = $_SERVER['APP_ENV'];
$_ENV['APP_DEBUG'] = $_SERVER['APP_DEBUG'];

require dirname(__DIR__).'/vendor/autoload.php';

if (filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOL)) {
    umask(0000);
}
