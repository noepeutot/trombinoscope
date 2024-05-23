<?php

use App\Controllers\Home;
use App\Controllers\Login;
use App\Controllers\Profile;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// TODO : changer get en post

$routes->setAutoRoute(false);

$routes->get('/', [Home::class, 'index']);
$routes->get('search?(:segment)', [Home::class, 'index/$1']);

$routes->get('profile/(:num)', [Profile::class, 'index/$1']);
$routes->get('profile/edit', [Profile::class, 'edit']);

$routes->match(['GET', 'POST'], 'login', [Login::class, 'index']);
$routes->get('logout', [Login::class, 'logout']);