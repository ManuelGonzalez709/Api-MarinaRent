<?php

use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UsuarioController;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\HasApiTokens;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', function (Request $request) {
    $usuario = Usuario::where('Email', $request->email)->first();

    if (!$usuario || !Hash::check($request->password, $usuario->Password)) {
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    $token = $usuario->createToken('token-api')->plainTextToken;

    return response()->json(['token' => $token], 200);
});

Route::middleware('auth:sanctum')->group(function () {


   


    // CRUD para usuarios, publicaciones y reservas
    Route::apiResource('usuarios', UsuarioController::class);
    Route::apiResource('publicaciones', PublicacionController::class);
    Route::apiResource('reservas', ReservaController::class);
});
