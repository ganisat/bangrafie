<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Configure Storage Path (Vercel / Serverless)
|--------------------------------------------------------------------------
| Vercel hanya mengizinkan write di /tmp. Kalau APP_STORAGE_PATH diset,
| kita arahkan storage Laravel ke sana.
*/

$storagePath =
    $_ENV['APP_STORAGE_PATH']
    ?? $_SERVER['APP_STORAGE_PATH']
    ?? getenv('APP_STORAGE_PATH')
    ?? null;

if (is_string($storagePath) && $storagePath !== '') {
    $app->useStoragePath($storagePath);
}

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
*/

return $app;
