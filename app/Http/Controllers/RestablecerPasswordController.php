<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\RestablecerPass;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RestablecerPasswordController extends Controller
{
    public function enviarRestablecimiento(Request $request)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        // Si la validación falla, retorna un error
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Datos inválidos',
                'messages' => $validator->errors()
            ], 422);
        }

        // Crear el enlace de restablecimiento (puedes generar un enlace real o simulado)
        $enlaceRestablecimiento = url('api/restablecer-password/' . $request->email);

        // Datos a enviar al correo
        $data = [
            'nombre' => 'Usuario', // Podrías obtener el nombre del usuario desde la base de datos
            'enlace' => $enlaceRestablecimiento,
        ];

        try {
            // Enviar el correo con texto plano
            Mail::raw("Hola, {$data['nombre']}\n\nHas solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para restablecerla:\n\n{$data['enlace']}\n\nSi no solicitaste el restablecimiento de contraseña, ignora este correo.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Restablecer Contraseña');
            });

            return response()->json(['success' => 'Correo enviado para restablecer la contraseña'], 200);
        } catch (\Exception $e) {
            // Si ocurre un error, manejar la excepción
            return response()->json(['error' => 'Hubo un problema al enviar el correo', 'message' => $e->getMessage()], 500);
        }
    }
}
