<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('category', 'CategoryController');
Route::apiResource('product', 'ProductController');


Route::post('telegram_notification', 'SendNotification@ToTelegram');