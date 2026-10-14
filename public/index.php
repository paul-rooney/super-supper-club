<?php

declare(strict_types=1);

// When using PHP's built-in dev server as a router script, let real files
// (built assets under /dist/, favicon, etc.) be served straight from disk.
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file)) {
        return false; // tell the server to serve the requested file as-is
    }
}

require __DIR__ . '/../app/bootstrap.php';
