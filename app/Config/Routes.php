<?php

use App\Controllers\Home;
use App\Controllers\Profile;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setAutoRoute(false);

$routes->get('/', [Home::class, 'index']);

$routes->get('/(:segment)', [Home::class, 'search/$1']);

$routes->get('profile/(:num)', [Profile::class, 'index/$1']);