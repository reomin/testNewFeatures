<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//apiResourceを使って、TodoControllerに対するRESTfulなルーティングを一括で定義する
Route::apiResource('todos', \App\Http\Controllers\TodoController::class)->middleware('auth:sanctum');
