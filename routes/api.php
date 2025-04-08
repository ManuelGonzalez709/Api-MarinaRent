<?php

use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;


Route::get('/usuarios',[UsuarioController::class,'index']);

Route::get('/reservas',[ReservaController::class,'index']);

Route::get('/publicaciones',[PublicacionController::class,'index']);