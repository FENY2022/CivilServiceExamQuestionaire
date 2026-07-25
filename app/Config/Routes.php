<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('HomeController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

$routes->get('/', 'HomeController::index');
$routes->get('about', 'HomeController::about');
$routes->post('register', 'AuthController::register');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::loginAction');
$routes->get('logout', 'AuthController::logout');
$routes->get('dashboard', 'DashboardController::index');
$routes->get('reviewer', 'ReviewerController::index');
$routes->post('api/submit-quiz', 'QuizController::submit');
$routes->get('admin', 'Admin\DashboardController::index');
$routes->post('admin/action', 'Admin\DashboardController::action');
