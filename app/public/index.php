<?php
session_start();

define('BASE_URL', '/KLTN/source/app/public/');
define('ROOT_PATH', dirname(__DIR__));

spl_autoload_register(function ($class) {

    $paths = [
        ROOT_PATH . "/core/$class.php",
    ];

    // scan tất cả modules
    foreach (glob(ROOT_PATH . "/modules/*/models/$class.php") as $file) {
        $paths[] = $file;
    }

    foreach (glob(ROOT_PATH . "/modules/*/controllers/$class.php") as $file) {
        $paths[] = $file;
    }

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

function asset($path) {
    return BASE_URL . 'assets/' . $path;
}

$module = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['module'] ?? 'dashboard');
$action = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['action'] ?? 'index');  

$controllerName = ucfirst($module) . "Controller";
$controllerPath = ROOT_PATH . "/modules/$module/controllers/$controllerName.php";

if (!file_exists($controllerPath)) {
    die("Module không tồn tại: $module");
}

require_once $controllerPath;

if (!class_exists($controllerName)) {
    die("Controller không tồn tại: $controllerName");
}

$controller = new $controllerName();

if (!method_exists($controller, $action)) {
    die("Action không tồn tại: $action");
}

$controller->$action();