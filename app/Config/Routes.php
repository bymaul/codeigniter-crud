<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/login', 'AuthController::login');
});
