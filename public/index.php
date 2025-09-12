<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Check if installer exists and installation is not completed
if (is_dir(__DIR__ . '/install') && !file_exists(__DIR__ . '/../storage/installer.lock')) {
    // Redirect to installer for non-install URLs
    $requestUri = $_SERVER['REQUEST_URI'];
    if (strpos($requestUri, '/install') !== 0) {
        header('Location: /install/');
        exit;
    }
}

// Auto-detect Laravel path for flexible deployment
$laravel_root = __DIR__;

// Check if we're in standard Laravel structure (public folder)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    $laravel_root = __DIR__ . '/..';
} 
// Check if we're in flat deployment (all files in same level)
elseif (file_exists(__DIR__ . '/vendor/autoload.php')) {
    $laravel_root = __DIR__;
} 
// Check if Laravel is in parent directory 
elseif (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    $laravel_root = dirname(__DIR__);
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $laravel_root.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $laravel_root.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $laravel_root.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
