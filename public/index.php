<?php
// Mulai session jika perlu
define('BASE_PATH', dirname(__DIR__));

// Autoload sederhana (bisa dikembangkan)
// require BASE_PATH . '/vendor/autoload.php';

// Routing sederhana
$url = $_GET['url'] ?? 'posts/index';
$url = explode('/', $url);

$controllerName = ucfirst($url[0]) . 'Controller';
$method = $url[1] ?? 'index';
$param = $url[2] ?? null;

$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . '.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    if (method_exists($controller, $method)) {
        if ($param !== null) {
            $controller->$method($param);
        } else {
            $controller->$method();
        }
    } else {
        echo "Method tidak ditemukan.";
    }
} else {
    echo "Controller tidak ditemukan.";
} 