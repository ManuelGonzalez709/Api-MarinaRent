<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class RestablecerPasswordEmailController extends Controller
{
    // Método para mostrar el formulario de restablecimiento
    public function mostrarFormulario($token, Request $request)
    {
        $email = $request->query('email'); // obtienes el email de los parámetros de la URL

        // Verificar si el correo está registrado con el modelo 'Usuario'
        $usuario = Usuario::where('Email', $email)->first();

        // Si no se encuentra el usuario, devolver un mensaje de error
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado: ' . $email
            ]);
        }

        // Si el correo es válido, devolver la vista para restablecer la contraseña
        return view('restablecer')->with([
            'email' => $email,
            'token' => $token
        ]);
    }


    public function restablecer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $registro = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$registro || !Hash::check($request->token, $registro->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado.'
            ], 403);
        }

        $usuario = Usuario::where('Email', $request->email)->first();

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        $usuario->Password = bcrypt($request->password);
        $usuario->save();

        // Eliminar el token después de usarlo
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña restablecida correctamente.'
        ]);
    }


}
