<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->resource('propostas');

$routes->post('propostas/create', 'Api\V1\PropostasController::create');
$routes->post('propostas/(:num)/submit', 'Api\V1\PropostasController::submit/$1');
$routes->post('propostas/(:num)/approve', 'Api/V1/PropostasController::approve/$1');
$routes->post('propostas/(:num)/reject', 'Api/V1/PropostasController::reject/$1');
$routes->post('propostas/(:num)/cancel', 'Api/V1/PropostasController::cancel/$1');