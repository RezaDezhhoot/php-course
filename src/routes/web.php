<?php

use Store\App\Controllers\Admin\DashboardController;
use Store\App\Controllers\Site\AuthController;
use Store\App\Middlewares\AuthMiddleware;
use Store\App\Middlewares\IsAdminMiddleware;

$r->addRoute('GET', '/admin', [
    'handler' => [DashboardController::class, 'show'],
    'middlwares' => [AuthMiddleware::class, IsAdminMiddleware::class],
]);
$r->addRoute('GET', '/login', [AuthController::class, 'login']);
$r->addRoute('POST', '/login', [AuthController::class, 'postLogin']);

// $r->addRoute('GET', '/products', ['App\Controllers\UserController', 'show']);
