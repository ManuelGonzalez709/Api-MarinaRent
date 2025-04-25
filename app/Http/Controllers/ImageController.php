<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function uploadImage($file)
    {
        // Validar archivo directamente aquí
        if (!$file->isValid() || !in_array($file->extension(), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return [
                'success' => false,
                'message' => 'Archivo no válido'
            ];
        }

        $path = $file->store('photos', 'public');

        return [
            'success' => true,
            'path' => asset('storage/' . $path)
        ];
    }

}

