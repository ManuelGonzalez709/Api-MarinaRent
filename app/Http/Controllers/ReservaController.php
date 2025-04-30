<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Publicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    public function getReservasPorUsuario($usuarioId)
    {
        $reservas = Reserva::where('usuario_id', $usuarioId)->get();

        return response()->json($reservas);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    public function getDisponibilidad(Request $request)
{
    $idPublicacion = $request->input('idPublicacion');
    $horaReserva = $request->input('horaReserva'); // Por ejemplo "23"

    // Validación básica
    if (!$idPublicacion || $horaReserva === null) {
        return response()->json(['error' => 'Datos incompletos'], 400);
    }

    // Buscar publicación
    $publicacion = Publicacion::find($idPublicacion);

    if (!$publicacion) {
        return response()->json(['error' => 'Publicación no encontrada'], 404);
    }

    // Obtener fecha del evento de la publicación
    $fechaEvento = Carbon::parse($publicacion->fecha_evento)->format('Y-m-d');

    // Formatear hora
    $horaFormateada = str_pad($horaReserva, 2, '0', STR_PAD_LEFT) . ':00:00';

    // Componer fecha y hora completa
    $fechaHoraCompleta = $fechaEvento . ' ' . $horaFormateada;

    // Verificar si existe reserva exacta en ese datetime
    $existeReserva = Reserva::where('publicacion_id', $idPublicacion)
        ->where('fecha_reserva', $fechaHoraCompleta)
        ->exists();

    return response()->json([
        'fecha_reserva_comprobada' => $fechaHoraCompleta,
        'disponible' => !$existeReserva,
    ]);
}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
