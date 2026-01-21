<?php

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

// writable hanya /tmp di Vercel Functions
putenv('APP_STORAGE_PATH=/tmp/storage');
$_ENV['APP_STORAGE_PATH'] = '/tmp/storage';
$_SERVER['APP_STORAGE_PATH'] = '/tmp/storage';

require __DIR__ . '/../public/index.php';
