<?php

use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\RestablecerPasswordController;
use App\Http\Controllers\RestablecerPasswordEmailController;
use App\Models\Usuario;
use App\Models\Publicacion;
use App\Models\Reserva;
use Illuminate\Support\Carbon;

Route::middleware('auth:sanctum')->group(function () {
    /*
    if (auth()->user()->Tipo == 'admin') {
        Route::post('generarDatos', function () {
            Publicacion::factory(10)->create();
            Usuario::factory(10)->create();
            Reserva::factory(10)->create();
            return response()->json(['message' => '10 publicaciones creadas correctamente.']);
        });
    }*/

    // CRUD para usuarios, publicaciones y reservas
    Route::apiResource('usuarios', UsuarioController::class);
    Route::apiResource('publicaciones', PublicacionController::class);
    Route::apiResource('reservas', ReservaController::class);

    // Publicaciones Ruta para obtener publicaciones Informativas y Alquilables
    Route::get('informativos', [PublicacionController::class, 'obtenerInformativos']);
    Route::get('alquilables', [PublicacionController::class, 'obtenerAlquilables']);
    Route::post('actualizar', [PublicacionController::class, 'update']);
    Route::post('actualizarFechaPublicacion', [ReservaController::class, 'actualizarFechaPublicacionYReservas']);

    //Usuario
    Route::get('usuario/getId', [UsuarioController::class, 'obtenerUsuarioAutenticado']);
    Route::get('isAdmin', [UsuarioController::class, 'obtenerAdmin']);
    Route::post('usuarios/actualizar', [UsuarioController::class, 'actualizar']);
    Route::post('mailTo', [UsuarioController::class, 'enviarCorreoPersonalizado']);
    Route::get('getData', [UsuarioController::class, 'obtenerDatosUsuarioAutenticado']);

    //Reservas
    Route::post('disponibilidadReserva', [ReservaController::class, 'getDisponibilidad']);
    Route::post('capacidadDisponible', [ReservaController::class, 'getCapacidadDisponible']);
    Route::get('obtenerReservasUsuario', [ReservaController::class, 'getReservasPorUsuario']);
    Route::get('obtenerReservasUsuario/{id}', [ReservaController::class, 'getReservasPorIdUsuario']);
    Route::get('obtenerReservasDetalladas', [ReservaController::class, 'obtenerReservasDetalladas']);
    Route::post('actualizarReservas', [ReservaController::class, 'update']);
    Route::post('intercambiarFechas', [ReservaController::class, 'intercambiarReserva']);
    
    //Subida de Imagenes (Imagenes singulares)
    Route::post('upload', [ImageController::class, 'upload']);

    //Obtener Datos Paginados
    Route::post('publicacionesPaginadas', [PublicacionController::class, 'obtenerPaginadas']);
    Route::post('reservasPaginadas', [ReservaController::class, 'paginarReservas']);
    Route::post('usuariosPaginados', [UsuarioController::class, 'paginarUsuarios']);
    Route::post('informativosPaginados', [PublicacionController::class, 'obtenerInformativosPaginados']);
    Route::post('alquilablesPaginados', [PublicacionController::class, 'obtenerAlquilablesPaginados']);

});

//Funcion para obtener la fecha y la hora del Server
Route::get('horaFecha', function () {
    $now = Carbon::now('Europe/Madrid'); // Establece la zona horaria manualmente

    return response()->json([
        'fecha' => $now->format('Y-m-d'),
        'hora' => $now->format('H:i:s')
    ]);
});

// Esto es para restablecer la contraseña
// envia al correo el enlace para restablecer la contraseña
Route::post('enviar-restablecimiento', [RestablecerPasswordController::class, 'enviarRestablecimiento'])->name('enviar.restablecimiento');
//Muestra el formulario al hacer click en el enlace del correo
Route::get('restablecer-password/{email}', [RestablecerPasswordEmailController::class, 'mostrarFormulario'])->name('mostrar.formulario.restablecer');
// Restablece la contraseña al dar click en restablecer contraseña en la pagina de restablecimiento de contraseña
Route::post('restablecer-password', [RestablecerPasswordEmailController::class, 'restablecer'])->name('restablecer.password');
//Retorna la vista de Success
Route::get('password-reset-success', function () {return view('password-reset-success');})->name('password.reset.success');

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

//Metodo de Login
Route::post('login', function (Request $request) {
    $usuario = Usuario::where('Email', $request->email)->first();

    if (!$usuario || !Hash::check($request->password, $usuario->Password)) {
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    $token = $usuario->createToken('token-api')->plainTextToken;

    return response()->json(['token' => $token], 200);
});

