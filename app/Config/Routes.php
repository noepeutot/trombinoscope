<?php

use App\Controllers\DashboardAdmin;
use App\Controllers\Home;
use App\Controllers\Login;
use App\Controllers\ModerationAdmin;
use App\Controllers\Profile;
use App\Controllers\UsersAdmin;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// TODO : changer get en post

$routes->setAutoRoute(false);

$routes->get('/', [Home::class, 'index']);
$routes->get('search?(:segment)', [Home::class, 'index/$1']);

$routes->get('profile/(:num)', [Profile::class, 'index/$1']);
$routes->match(['GET', 'POST'], 'profile/edit', [Profile::class, 'edit']);
$routes->match(['GET'], 'profile/edit/delete', [Profile::class, 'deletePhoto']);

$routes->get('updateDB', [Home::class, 'updateDB']);

$routes->get('backoffice/dashboard', [DashboardAdmin::class, 'index']);
$routes->get('backoffice/users', [UsersAdmin::class, 'index']);
$routes->get('backoffice/moderation', [ModerationAdmin::class, 'index']);

$routes->match(['GET', 'POST'], 'login', [Login::class, 'index']);
$routes->get('logout', [Login::class, 'logout']);