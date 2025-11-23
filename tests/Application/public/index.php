<?php

declare(strict_types=1);

use Galcvua\JwtRefreshBundle\Tests\Application\Kernel;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../../bootstrap.php';

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
