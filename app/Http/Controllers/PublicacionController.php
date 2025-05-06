<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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
        Log::info('Iniciando método store para crear publicación');

        try {
            Log::info('Validando la solicitud...', ['request' => $request->all()]);

            $request->validate([
                'Titulo' => 'required|string|max:255',
                'Descripcion' => 'required|string|max:1000',
                'Fecha_evento' => 'required|date',
                'Tipo' => 'required|string|max:255',
                'Precio' => 'required|numeric',
                "Aforo_maximo" => 'required|numeric',
                'imagenes' => 'required|array|max:4',
                'imagenes.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048'
            ]);

            $publicacion = new Publicacion([
                'Titulo' => $request->input('Titulo'),
                'Descripcion' => $request->input('Descripcion'),
                'Fecha_evento' => $request->input('Fecha_evento'),
                "Aforo_maximo" => $request->input('Aforo_maximo'),
                'Tipo' => $request->input('Tipo'),
                'Precio' => $request->input('Precio')
            ]);

            Log::info('Datos de la publicación preparados.', ['publicacion' => $publicacion]);

            $imageController = new ImageController();

            $imagePaths = [];
            $uploadFailed = false;
            $errorMessages = [];

            foreach ($request->file('imagenes') as $image) {
                Log::info('Subiendo imagen...', ['imagen' => $image->getClientOriginalName()]);

                $responseData = $imageController->uploadImage($image);

                if (!$responseData['success']) {
                    $uploadFailed = true;
                    $errorMessages[] = 'No se pudo subir la imagen: ' . $image->getClientOriginalName();
                    Log::error('Fallo al subir imagen.', ['imagen' => $image->getClientOriginalName()]);
                } else {
                    $imagePaths[] = basename($responseData['path']);
                    Log::info('Imagen subida exitosamente.', ['ruta' => $responseData['path']]);
                }
            }

            if ($uploadFailed) {
                Log::warning('Error en la subida de imágenes.', ['errores' => $errorMessages]);

                return response()->json([
                    'success' => false,
                    'message' => 'Algunas imágenes no se pudieron subir.',
                    'errors' => $errorMessages
                ], 400);
            }

            $publicacion->Imagen = implode(';', $imagePaths);
            $publicacion->save();

            Log::info('Publicación guardada exitosamente.', ['id' => $publicacion->id]);

            return response()->json([
                'success' => true,
                'message' => 'Publicación y imágenes guardadas exitosamente.',
                'data' => $publicacion
            ]);
        } catch (\Exception $e) {
            Log::error('Error inesperado al guardar la publicación.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        Log::info('Iniciando método update para actualizar publicación', ['request' => $request->all()]);
        try {
            Log::info('Validando la solicitud...', ['request' => $request->all()]);

            // Validación de los parámetros en el body
            $request->validate([
                'id_publicacion' => 'required|integer|exists:publicaciones,id',  // Validación para el ID
                'Titulo' => 'required|string|max:255',
                'Descripcion' => 'required|string|max:1000',
                'Fecha_evento' => 'required|date',
                'Tipo' => 'required|string|max:255',
                'Precio' => 'required|numeric',
                'Aforo_maximo' => 'required|numeric',
                'imagenes' => 'sometimes|array|max:4',
                'imagenes.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048'
            ]);

            // Obtener el id_publicacion desde el body de la solicitud
            $id = $request->input('id_publicacion');

            // Buscar la publicación por el ID
            $publicacion = Publicacion::find($id);

            if (!$publicacion) {
                Log::warning('Publicación no encontrada.', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Publicación no encontrada.'
                ], 404);
            }

            // Eliminar las imágenes antiguas antes de actualizar
            if ($publicacion->imagen) {
                $imagenesAntiguas = explode(';', $publicacion->imagen);
                foreach ($imagenesAntiguas as $imagen) {
                    // Extraemos el nombre del archivo de la URL
                    $imagenNombre = basename($imagen); // Obtiene solo el nombre del archivo de la URL
                    $rutaImagen = public_path('storage/photos/' . $imagenNombre);

                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen); // Elimina la imagen del sistema de archivos
                        Log::info('Imagen eliminada del sistema de archivos.', ['imagen' => $imagenNombre]);
                    }
                }
            }

            // Actualizar los datos de la publicación
            $publicacion->Titulo = $request->input('Titulo');
            $publicacion->Descripcion = $request->input('Descripcion');
            $publicacion->Fecha_evento = $request->input('Fecha_evento');
            $publicacion->Tipo = $request->input('Tipo');
            $publicacion->Precio = $request->input('Precio');
            $publicacion->Aforo_maximo = $request->input('Aforo_maximo');

            $imagePaths = [];

            if ($request->hasFile('imagenes')) {
                $imageController = new ImageController();
                $uploadFailed = false;
                $errorMessages = [];

                foreach ($request->file('imagenes') as $image) {
                    Log::info('Subiendo imagen en actualización...', ['imagen' => $image->getClientOriginalName()]);

                    $responseData = $imageController->uploadImage($image);

                    if (!$responseData['success']) {
                        $uploadFailed = true;
                        $errorMessages[] = 'No se pudo subir la imagen: ' . $image->getClientOriginalName();
                        Log::error('Fallo al subir imagen.', ['imagen' => $image->getClientOriginalName()]);
                    } else {
                        $imagePaths[] = basename($responseData['path']);
                        Log::info('Imagen subida exitosamente.', ['ruta' => $responseData['path']]);
                    }
                }

                if ($uploadFailed) {
                    Log::warning('Error en la subida de imágenes.', ['errores' => $errorMessages]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Algunas imágenes no se pudieron subir.',
                        'errors' => $errorMessages
                    ], 400);
                }

                // Solo actualiza si hay nuevas imágenes
                $publicacion->Imagen = implode(';', $imagePaths);
            }

            // Guardar los cambios en la publicación
            $publicacion->save();
            Log::info('Publicación actualizada exitosamente.', ['id' => $publicacion->id]);

            return response()->json([
                'success' => true,
                'message' => 'Publicación actualizada exitosamente.',
                'data' => $publicacion
            ]);
        } catch (\Exception $e) {
            Log::error('Error inesperado al actualizar la publicación.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado.',
                'error' => $e->getMessage()
            ], 500);
        }
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
