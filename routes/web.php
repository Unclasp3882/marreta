<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ProxyController as ApiProxyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\ShareTargetController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/manifest.json', ManifestController::class)->name('manifest');

// Share target entry point (PWA share_target)
Route::get('/p', ShareTargetController::class);
Route::get('/p/', ShareTargetController::class);

// Proxy route — captures the full URL including slashes
Route::get('/p/{url}', ProxyController::class)
    ->where('url', '.+')
    ->name('proxy');

// API
Route::get('/api', fn () => redirect('/'));
Route::get('/api/', fn () => redirect('/'));
Route::get('/api/{url}', ApiProxyController::class)
    ->where('url', '.+')
    ->name('api.proxy');
