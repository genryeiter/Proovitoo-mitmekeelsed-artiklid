<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ArticleController;

Route::get('/', function () {
    return view('welcome');
});

// Public routes (no API key required)
Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/{path}', [ArticleController::class, 'show']);
});

// Private routes (require API key)
Route::middleware('api.guard')->prefix('private')->group(function () {
    Route::prefix('articles')->group(function () {
        Route::get('/', [ArticleController::class, 'index']);
        Route::get('/{path}', [ArticleController::class, 'show']);
        Route::post('/', [ArticleController::class, 'store']);
        Route::put('/{article}', [ArticleController::class, 'update']);
    });
});
