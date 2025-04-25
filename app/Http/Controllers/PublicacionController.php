<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\Request;
use App\Http\Controllers\ImageController;

class PublicacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $publicaciones = Publicacion::all();
        return response()->json(data: $publicaciones);
    }

    public function obtenerInformativos()
    {
        $publicaciones = Publicacion::where('tipo', 'informativo')->get();

        if ($publicaciones->isEmpty()) {
            return response()->json([], 404);
        }

        return response()->json($publicaciones);
    }
    public function obtenerAlquilables()
    {
        $publicaciones = Publicacion::where('tipo', 'alquilable')->get();

        if ($publicaciones->isEmpty()) {
            return response()->json([], 404);
        }

        return response()->json($publicaciones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'Titulo' => 'required|string|max:255',
        'Descripcion' => 'required|string|max:1000',
        'Fecha_publicacion' => 'required|date',
        'Tipo' => 'required|string|max:255',
        'Precio' => 'required|numeric',
        'imagenes' => 'required|array|max:4',
        'imagenes.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048'
    ]);

    $publicacion = new Publicacion([
        'Titulo' => $request->input('Titulo'),
        'Descripcion' => $request->input('Descripcion'),
        'Fecha_publicacion' => $request->input('Fecha_publicacion'),
        'Tipo' => $request->input('Tipo'),
        'Precio' => $request->input('Precio')
    ]);

    $imageController = new ImageController();

    $imagePaths = [];
    $uploadFailed = false;
    $errorMessages = [];

    foreach ($request->file('imagenes') as $image) {
        $responseData = $imageController->uploadImage($image);

        if (!$responseData['success']) {
            $uploadFailed = true;
            $errorMessages[] = 'No se pudo subir la imagen: ' . $image->getClientOriginalName();
        } else {
            // Solo el nombre del archivo
            $imagePaths[] = basename($responseData['path']);
        }
    }

    if ($uploadFailed) {
        return response()->json([
            'success' => false,
            'message' => 'Algunas imágenes no se pudieron subir.',
            'errors' => $errorMessages
        ], 400);
    }

    // Unir los nombres con punto y coma
    $publicacion->Imagen = implode(';', $imagePaths);
    $publicacion->save();

    return response()->json([
        'success' => true,
        'message' => 'Publicación y imágenes guardadas exitosamente.',
        'data' => $publicacion
    ]);
}




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $publicacion = Publicacion::find($id);

        if (!$publicacion) {
            return response()->json(['message' => 'Publicación no encontrada'], 404);
        }

        return response()->json($publicacion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
