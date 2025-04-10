<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        // Validar que el archivo sea una imagen
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        // Verificar si el archivo fue subido correctamente
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            // Almacenar la imagen en el disco 'public'
            $path = $file->store('photos', 'public');
            
            // Devolver la URL de la imagen
            return response()->json([
                'success' => true,
                'path' => asset('storage/' . $path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se pudo subir la imagen.'
        ], 400);
    }
}

