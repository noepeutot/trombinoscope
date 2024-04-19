<?php

use App\Controllers\Profile;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setAutoRoute(false);

$routes->get('/', 'Home::index');

$routes->get('profile/(:num)', [Profile::class, 'index/$1']);