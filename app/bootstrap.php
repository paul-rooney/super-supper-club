<?php

declare(strict_types=1);

// --- Wire up the framework pieces -------------------------------------------
// Explicit requires are the most minimal thing that works. When this grows,
// swap in spl_autoload_register() or Composer's PSR-4 autoloader.
require __DIR__ . '/vite.php';
require __DIR__ . '/Router.php';
require __DIR__ . '/Controller.php';

foreach (glob(__DIR__ . '/Controllers/*.php') as $file) {
    require $file;
}

// --- Routing table ----------------------------------------------------------
$router = new Router();
$router->get('/',      [HomeController::class,  'index']);
$router->get('/about', [AboutController::class, 'index']);

// --- Handle the current request ---------------------------------------------
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
