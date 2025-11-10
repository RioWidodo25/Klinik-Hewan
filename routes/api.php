<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Midtrans Notification Webhook
Route::post('/midtrans/notification', [\App\Http\Controllers\Petshop\MidtransController::class, 'notification']);
