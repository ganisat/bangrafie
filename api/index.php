<?php

// =========================
// Vercel: writable hanya /tmp
// =========================
$storage = '/tmp/storage';

// Folder yang dibutuhkan Laravel
@mkdir($storage . '/framework/cache/data', 0777, true);
@mkdir($storage . '/framework/sessions', 0777, true);
@mkdir($storage . '/framework/views', 0777, true);
@mkdir($storage . '/logs', 0777, true);

// Helper set env untuk PHP + Laravel
$setEnv = function (string $key, string $value) {
    putenv($key . '=' . $value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
};

// Storage dipindah ke /tmp
$setEnv('APP_STORAGE_PATH', $storage);
$setEnv('VIEW_COMPILED_PATH', $storage . '/framework/views');

// PENTING: cache file path dipindah ke /tmp (biar tidak nulis ke bootstrap/cache)
$setEnv('APP_CONFIG_CACHE',   '/tmp/config.php');
$setEnv('APP_ROUTES_CACHE',   '/tmp/routes.php');
$setEnv('APP_SERVICES_CACHE', '/tmp/services.php');
$setEnv('APP_PACKAGES_CACHE', '/tmp/packages.php');
$setEnv('APP_EVENTS_CACHE',   '/tmp/events.php');

// Entry ke Laravel public/index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

require __DIR__ . '/../public/index.php';
