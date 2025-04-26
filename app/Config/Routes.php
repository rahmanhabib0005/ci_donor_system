<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('donors', 'DonorController::index');
$routes->post('donors/datatable', 'DonorController::datatable');
$routes->get('donors/getDistricts', 'DonorController::getDistricts');
$routes->post('donors/getThanas', 'DonorController::getThanas');
$routes->post('donors/getFilterThanas', 'DonorController::getFilterThanas');
$routes->post('donors/create', 'DonorController::create');
