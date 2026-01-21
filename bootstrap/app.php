<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$basePath = $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__);

$app = Application::configure(basePath: $basePath)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->create();

/**
 * Vercel / Serverless: arahkan storage ke /tmp jika ada APP_STORAGE_PATH
 */
$storagePath =
    $_ENV['APP_STORAGE_PATH']
    ?? $_SERVER['APP_STORAGE_PATH']
    ?? getenv('APP_STORAGE_PATH')
    ?? null;

if (is_string($storagePath) && $storagePath !== '') {
    $app->useStoragePath($storagePath);
}

return $app;
