<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/clientes',      [ClienteController::class, 'index']);
Route::get('/clientes/{id}', [ClienteController::class, 'show']);
Route::post('/clientes',     [ClienteController::class, 'store']);
