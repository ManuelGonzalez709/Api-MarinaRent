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
    public function getCapacidadDisponible(Request $request)
    {
        $idPublicacion = $request->input('idPublicacion');

        if (!$idPublicacion) {
            return response()->json(['error' => 'ID de publicación requerido'], 400);
        }

        $publicacion = Publicacion::find($idPublicacion);

        if (!$publicacion) {
            return response()->json(['error' => 'Publicación no encontrada'], 404);
        }

        $aforoMaximo = $publicacion->aforo_maximo;

        // Suma de todas las personas ya reservadas
        $personasReservadas = Reserva::where('publicacion_id', $idPublicacion)
            ->sum('personas');

        // Cálculo de personas disponibles
        $personasDisponibles = max($aforoMaximo - $personasReservadas, 0);

        // El usuario puede reservar entre 1 y 4 personas, sin superar la capacidad restante
        $maxReservables = min(4, $personasDisponibles);
        $minReservables = $maxReservables > 0 ? 1 : 0;

        return response()->json([
            'aforo_maximo' => $aforoMaximo,
            'personas_reservadas' => $personasReservadas,
            'personas_disponibles' => $personasDisponibles,
            'min_reservables' => $minReservables,
            'max_reservables' => $maxReservables
        ]);
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
