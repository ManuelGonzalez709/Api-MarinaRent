<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;

class RestablecerPasswordEmailController extends Controller
{
    // Método para mostrar el formulario de restablecimiento
    public function mostrarFormulario($email)
    {
        // Verificar si el correo está registrado con el modelo 'Usuario'
        $usuario = Usuario::where('Email', $email)->first();

        // Si no se encuentra el usuario, devolver un mensaje de error
        if (!$usuario) {
            return redirect()->route('login')->with('error', 'No se encontró un usuario con ese correo.');
        }

        // Si el correo es válido, devolver la vista para restablecer la contraseña
        return view('restablecer')->with('email', $email); // Aquí se cambia 'auth.restablecer' a 'restablecer'
    }

    public function restablecer(Request $request)
    {
        // Validación manual para controlar el formato de respuesta
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar usuario por email
        $usuario = Usuario::where('Email', $request->email)->first();

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un usuario con ese correo.'
            ], 404);
        }

        // Guardar la nueva contraseña
        $usuario->Password = bcrypt($request->password);
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña restablecida correctamente.'
        ]);
    }



}
