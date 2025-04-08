<?php

use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/usuarios',[UsuarioController::class,'index']);
Route::post('/usuarios',[UsuarioController::class,'store']);
Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

//Reservas 
Route::get('/reservas',[ReservaController::class,'index']);




//Publicaciones
Route::get('/publicaciones',[PublicacionController::class,'index']);