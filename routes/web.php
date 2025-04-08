<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UsuarioController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/usuarios',[UsuarioController::class,'index']);

Route::get('/reservas',[ReservaController::class,'index']);

Route::get('/publicaciones',[PublicacionController::class,'index']);