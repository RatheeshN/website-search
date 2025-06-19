<?php
use App\Http\Controllers\Api\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/search', [SearchController::class, 'search']);
Route::get('/search/suggestions', [SearchController::class, 'suggestions']);
Route::get('/search/logs', [SearchController::class, 'logs'])->middleware('admin');