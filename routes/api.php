<?php

use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UsuarioController;
use App\Models\Usuario;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\RestablecerPasswordController;
use App\Http\Controllers\RestablecerPasswordEmailController;

Route::middleware('auth:sanctum')->group(function () {

    // CRUD para usuarios, publicaciones y reservas
    Route::apiResource('usuarios', UsuarioController::class);
    Route::apiResource('publicaciones', PublicacionController::class);
    Route::apiResource('reservas', ReservaController::class);

    //Usuario
    Route::get('usuario/getId', [UsuarioController::class, 'obtenerUsuarioAutenticado']);

    //Reservas
    Route::get('reservas/usuario/{usuarioId}', [ReservaController::class, 'getReservasPorUsuario']);

    //Subida de Imagenes
    Route::post('upload', [ImageController::class, 'upload']);

    // Ruta para enviar el correo con el enlace de restablecimiento de contraseña
   

});

Route::get('restablecer-password/{email}', [RestablecerPasswordEmailController::class, 'mostrarFormulario'])->name('mostrar.formulario.restablecer');
Route::post('restablecer-password', [RestablecerPasswordEmailController::class, 'restablecer'])->name('restablecer.password');
Route::post('enviar-restablecimiento', [RestablecerPasswordController::class, 'enviarRestablecimiento'])->name('enviar.restablecimiento');


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

