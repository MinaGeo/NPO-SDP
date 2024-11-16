<?php
// src/index.php or src/15-index.php
// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
// Autoload classes (if not using Composer, you'll need to implement an autoloader or require files manually)
spl_autoload_register(function ($class_name) {
    $paths = [
        __DIR__ . '/controllers/' . $class_name . '.php',
        __DIR__ . '/' . $class_name . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Load configuration
$configs = require_once 'server-configs.php';
require_once "./db_setup.php";

// Initialize Router
$router = new Router($configs);

// Get the current URL path
// Assuming URL is in the format: http://localhost/Lab%2003/15-index.php/route
// We'll parse the REQUEST_URI to extract the path after '15-index.php'
$requestUri = $_SERVER['REQUEST_URI'];

// Parse URL to get the path after '15-index.php'
$scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /Lab%2003/15-index.php
$basePath = dirname($scriptName);      // e.g., /Lab%2003

// Remove the base path and script name from the request URI
$path = substr($requestUri, strlen($basePath . '/' . $configs->URL_SUBFOLDER));

// Ensure the path starts with a '/'
if ($path === false) {
    $path = '/';
} else {
    $path = '/' . ltrim($path, '/');
}

// Route the request
$router->route($path);

$_SESSION['USER_ID']=-1;