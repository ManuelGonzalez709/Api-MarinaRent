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

    public function obtenerPaginadas(Request $request)
    {
        // Constante de elementos por página
        $ELEMENTOS_POR_PAGINA = 8;

        // Obtener número de página desde la query (por defecto 1)
        $pagina = (int) $request->input('pagina', 1);

        try {
            // Obtener publicaciones con paginación
            $publicaciones = Publicacion::paginate($ELEMENTOS_POR_PAGINA, ['*'], 'page', $pagina);

            // Preparar respuesta con metadatos personalizados
            return response()->json([
                'success' => true,
                'data' => $publicaciones->items(),
                'page' => $publicaciones->currentPage(),
                'totalPages' => $publicaciones->lastPage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener publicaciones paginadas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    ////Paginacion de publicaciones por tipo 
    public function obtenerAlquilablesPaginados(Request $request)
    {
        $ELEMENTOS_POR_PAGINA = 8;
        $pagina = (int) $request->input('pagina', 1);

        try {
            $publicaciones = Publicacion::where('tipo', 'alquilable')
                ->whereDate('fecha_evento', '>=', Carbon::today())
                ->paginate($ELEMENTOS_POR_PAGINA, ['*'], 'page', $pagina);

            return response()->json([
                'success' => true,
                'data' => $publicaciones->items(),
                'page' => $publicaciones->currentPage(),
                'totalPages' => $publicaciones->lastPage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener publicaciones alquilables paginadas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerInformativosPaginados(Request $request)
    {
        $ELEMENTOS_POR_PAGINA = 8;
        $pagina = (int) $request->input('pagina', 1);

        try {
            $publicaciones = Publicacion::where('tipo', 'informativo')
                ->whereDate('fecha_evento', '>=', Carbon::today())
                ->paginate($ELEMENTOS_POR_PAGINA, ['*'], 'page', $pagina);

            return response()->json([
                'success' => true,
                'data' => $publicaciones->items(),
                'page' => $publicaciones->currentPage(),
                'totalPages' => $publicaciones->lastPage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener publicaciones informativas paginadas.',
                'error' => $e->getMessage()
            ], 500);
        }
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
            // Validación de los datos
            $request->validate([
                'id_publicacion' => 'required|integer|exists:publicaciones,id',
                'Titulo' => 'required|string|max:255',
                'Descripcion' => 'required|string|max:1000',
                'Fecha_evento' => 'required|date',
                'Tipo' => 'required|string|max:255',
                'Precio' => 'required|numeric',
                'Aforo_maximo' => 'required|numeric',
                'imagenes' => 'sometimes|array|max:4',
                'imagenes.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048',
                'imagenes_existentes' => 'sometimes|array',
                'imagenes_existentes.*' => 'string'
            ]);
            $id = $request->input('id_publicacion');
            $publicacion = Publicacion::find($id);

            if (!$publicacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Publicación no encontrada.'
                ], 404);
            }

            // Obtener imágenes antiguas y existentes a conservar
            $imagenesAntiguas = $publicacion->Imagen ? explode(';', $publicacion->Imagen) : [];
            $imagenesExistentes = $request->input('imagenes_existentes', []);

            // Asegurarse de que es array
            if (is_string($imagenesExistentes)) {
                $imagenesExistentes = explode(';', $imagenesExistentes);
            }

            // Determinar imágenes a eliminar
            $imagenesAEliminar = array_diff($imagenesAntiguas, $imagenesExistentes);

            foreach ($imagenesAEliminar as $imagen) {
                $rutaImagen = public_path('storage/photos/' . basename($imagen));
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }

            // Actualizar datos principales
            $publicacion->Titulo = $request->input('Titulo');
            $publicacion->Descripcion = $request->input('Descripcion');
            $publicacion->Fecha_evento = $request->input('Fecha_evento');
            $publicacion->Tipo = $request->input('Tipo');
            $publicacion->Precio = $request->input('Precio');
            $publicacion->Aforo_maximo = $request->input('Aforo_maximo');

            // Inicializar lista con las imágenes que se conservan
            $imagePaths = $imagenesExistentes;

            // Subir nuevas imágenes si existen
            if ($request->hasFile('imagenes')) {
                $imageController = new ImageController();
                $uploadFailed = false;
                $errorMessages = [];

                foreach ($request->file('imagenes') as $image) {
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
                if ($uploadFailed)
                    return response()->json(['success' => false, 'message' => 'Algunas imágenes no se pudieron subir.', 'errors' => $errorMessages], 400);

            }
            // Guardar rutas actualizadas de imágenes
            $publicacion->Imagen = implode(';', $imagePaths);
            $publicacion->save();

            return response()->json([
                'success' => true,
                'message' => 'Publicación actualizada exitosamente.',
                'data' => $publicacion
            ]);

        } catch (\Exception $e) {
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
        $publicacion = Publicacion::find($id);

        if (!$publicacion)
            return response()->json(['message' => 'Publicación no encontrada.'], 404);

        try {
            // Eliminar imágenes asociadas
            if ($publicacion->imagen) {
                $imagenes = explode(';', $publicacion->imagen);

                foreach ($imagenes as $imagen) {
                    $rutaImagen = public_path('storage/photos/' . basename($imagen));
                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen);
                    }
                }
            }

            // Eliminar la publicación
            $publicacion->delete();

            return response()->json(['message' => 'Publicación e imágenes eliminadas correctamente.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la publicación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
