<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('docs', 'DocsController::index');
$routes->get('docs/openapi.yaml', 'DocsController::yaml');
$routes->group('api/v1', function($routes) {

$routes->get('clientes', 'Api\V1\ClientesController::index');
$routes->post('clientes', 'Api\V1\ClientesController::create');
$routes->get('clientes/(:num)', 'Api\V1\ClientesController::show/$1');
$routes->patch('clientes/(:num)', 'Api\V1\ClientesController::update/$1');

$routes->post('propostas', 'Api\V1\PropostasController::create');
$routes->patch('propostas/(:num)', 'Api\V1\PropostasController::update/$1');
$routes->post('propostas/(:num)/submit', 'Api\V1\PropostasController::submit/$1');
$routes->post('propostas/(:num)/approve', 'Api\V1\PropostasController::approve/$1');
$routes->post('propostas/(:num)/reject', 'Api\V1\PropostasController::reject/$1');
$routes->post('propostas/(:num)/cancel', 'Api\V1\PropostasController::cancel/$1');
$routes->get('propostas', 'Api\V1\PropostasController::index');
$routes->get('propostas/(:num)', 'Api\V1\PropostasController::show/$1');
$routes->get('propostas/(:num)/auditoria', 'Api\V1\PropostasController::auditoria/$1');
});