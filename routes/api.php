<?php

use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\SellerController;
use Illuminate\Support\Facades\Route;

Route::get('/promos', [PromoController::class, 'index']);
Route::get('/promos/{id}', [PromoController::class, 'show']);
Route::get('/sellers', [SellerController::class, 'index']);
