<?php

$storage = '/tmp/storage';

// Buat folder yang dibutuhkan Laravel
@mkdir($storage . '/framework/cache/data', 0777, true);
@mkdir($storage . '/framework/sessions', 0777, true);
@mkdir($storage . '/framework/views', 0777, true);
@mkdir($storage . '/logs', 0777, true);

// set env agar bootstrap/app.php memakai /tmp
putenv('APP_STORAGE_PATH=' . $storage);
$_ENV['APP_STORAGE_PATH'] = $storage;
$_SERVER['APP_STORAGE_PATH'] = $storage;

putenv('VIEW_COMPILED_PATH=' . $storage . '/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = $storage . '/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = $storage . '/framework/views';

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

require __DIR__ . '/../public/index.php';
