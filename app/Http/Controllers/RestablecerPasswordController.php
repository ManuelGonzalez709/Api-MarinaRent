<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\RestablecerPass;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RestablecerPasswordController extends Controller
{
    public function enviarRestablecimiento(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:usuarios,Email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Datos inválidos',
                'messages' => $validator->errors()
            ], 422);
        }

        $token = Str::random(64);

        // Guardar el token en la tabla password_resets
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $enlaceRestablecimiento = url('api/restablecer-password/' . $token . '?email=' . urlencode($request->email));

        Mail::raw("Hola,\n\nHaz clic en el siguiente enlace para restablecer tu contraseña:\n\n{$enlaceRestablecimiento}", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Restablecer Contraseña');
        });

        return response()->json(['success' => 'Correo enviado para restablecer la contraseña'], 200);
    }
}
