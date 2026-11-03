<?php
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);

require_once __DIR__ . "/includes/connection.php";
require_once __DIR__ . "/includes/functions.php";
require_once __DIR__ . "/models/User.php";
require_once __DIR__ . "/controllers/AuthController.php";

$auth = new AuthController(new User($connection));

$routes = [
    'home' => 'home',
    'login' => 'login',
    'register' => 'register',
    'logout' => 'logout'
];
$page = $_GET['page'] ?? 'home';

if (!isset($routes[$page])) {
    http_response_code(404);
    require __DIR__ . "/views/404.php";
    exit;
}

$auth->{$routes[$page]}();
