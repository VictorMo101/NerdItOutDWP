<?php
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);

require_once __DIR__ . "/includes/connection.php";
require_once __DIR__ . "/includes/functions.php";
require_once __DIR__ . "/models/User.php";
require_once __DIR__ . "/models/Post.php";
require_once __DIR__ . "/controllers/AuthController.php";
require_once __DIR__ . "/controllers/PostController.php";

$auth = new AuthController(new User($connection));
$postController = new PostController(new Post($connection));

$routes = [
    'home' => [$postController, 'index'],
    'create_post' => [$postController, 'create'],
    'delete_post' => [$postController, 'delete'],
    'login' => [$auth, 'login'],
    'register' => [$auth, 'register'],
    'logout' => [$auth, 'logout']
];
$page = $_GET['page'] ?? 'home';

if (!isset($routes[$page])) {
    http_response_code(404);
    require __DIR__ . "/views/404.php";
    exit;
}

call_user_func($routes[$page]);
