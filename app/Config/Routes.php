<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/login', 'AuthController::login');
    $routes->get('posts', 'PostController::index');
    $routes->get('posts/(:num)', 'PostController::show/$1');

    $routes->group('', ['filter' => 'jwt'], function ($routes) {
        $routes->post('posts', 'PostController::create');
        $routes->put('posts/(:num)', 'PostController::update/$1');
        $routes->delete('posts/(:num)', 'PostController::delete/$1');
    });
});
