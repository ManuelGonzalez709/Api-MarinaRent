<?php

use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UsuarioController;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;



Route::middleware('auth:sanctum')->group(function () {

    // CRUD para usuarios, publicaciones y reservas
    Route::apiResource('usuarios', UsuarioController::class);
    Route::apiResource('publicaciones', PublicacionController::class);
    Route::apiResource('reservas', ReservaController::class);

    //Usuario
    Route::get('usuario/getId', [UsuarioController::class, 'obtenerUsuarioAutenticado']);
    
    //Reservas
    Route::get('reservas/usuario/{usuarioId}', [ReservaController::class, 'getReservasPorUsuario']);

});

////Register
Route::post('register', function (Request $request) {
    $validated = $request->validate([
        'Nombre' => 'required|string|max:255',
        'Apellidos' => 'required|string|max:255',
        'Fecha_nacimiento' => 'required|date',
        'Email' => 'required|email|unique:usuarios,Email',
        'Password' => 'required|string|min:6|confirmed', 
    ]);

    // Crear un nuevo usuario
    $usuario = Usuario::create([
        'Nombre' => $validated['Nombre'],
        'Apellidos' => $validated['Apellidos'],
        'Fecha_nacimiento' => $validated['Fecha_nacimiento'],
        'Email' => $validated['Email'],
        'Password' => Hash::make($validated['Password']),
    ]);

    // Crear un token para el nuevo usuario
    $token = $usuario->createToken('token-api')->plainTextToken;

    // Devolver el token al cliente
    return response()->json(['token' => $token], 201);
});


Route::post('login', function (Request $request) {
    $usuario = Usuario::where('Email', $request->email)->first();

    if (!$usuario || !Hash::check($request->password, $usuario->Password)) {
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    $token = $usuario->createToken('token-api')->plainTextToken;

    return response()->json(['token' => $token], 200);
});

