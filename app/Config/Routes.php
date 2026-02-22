<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('docs', 'DocsController::index');
$routes->get('docs/openapi.yaml', 'DocsController::yaml');
$routes->group('api/v1', function($routes) {

    // CLIENTES
    $routes->resource('clientes', [
        'controller' => 'Api\V1\ClientesController'
    ]);

    // PROPOSTAS (CRUD base)
    $routes->resource('propostas', [
        'controller' => 'Api\V1\PropostasController'
    ]);

    // TRANSIÇÕES DE STATUS
    $routes->post('propostas/(:num)/submit',  'Api\V1\PropostasController::submit/$1');
    $routes->post('propostas/(:num)/approve', 'Api\V1\PropostasController::approve/$1');
    $routes->post('propostas/(:num)/reject',  'Api\V1\PropostasController::reject/$1');
    $routes->post('propostas/(:num)/cancel',  'Api\V1\PropostasController::cancel/$1');

    // AUDITORIA
$routes->get(
    'propostas/(:num)/auditoria',
    'Api\V1\PropostasController::auditoria/$1'
);
});