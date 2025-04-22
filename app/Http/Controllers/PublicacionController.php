<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\Request;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
