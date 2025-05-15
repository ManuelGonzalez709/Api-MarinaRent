<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Publicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\CorreoPersonalizado;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservas = Reserva::all();
        return response()->json($reservas);
    }

    public function cancelarReservaUsuario($idReserva)
    {

    }

    public function getReservasPorUsuario(Request $request)
    {
        $usuario = Auth::guard('sanctum')->user();
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        // Cargar reservas junto con el título de la publicación
        $reservas = $usuario->reservas()->with(['publicacion:id,titulo,imagen'])->get();


        return response()->json($reservas);
    }

    public function actualizarFechaPublicacionYReservas(Request $request)
    {
        $validated = $request->validate([
            'publicacion_id' => 'required|exists:publicaciones,id',
            'nueva_fecha_evento' => 'required|date_format:Y-m-d', // Solo la fecha, sin hora
        ]);

        $publicacion = Publicacion::findOrFail($validated['publicacion_id']);
        $fechaAnterior = $publicacion->fecha_evento;
        $nuevaFechaEvento = $validated['nueva_fecha_evento'];

        // Actualizar la fecha del evento de la publicación
        $publicacion->fecha_evento = $nuevaFechaEvento;
        $publicacion->save();

        // Obtener todas las reservas relacionadas con esta publicación
        $reservas = Reserva::with('usuario')
            ->where('publicacion_id', $publicacion->id)
            ->get();

        $reservasActualizadas = [];

        foreach ($reservas as $reserva) {
            // Extraer la hora original de la reserva
            $horaOriginal = Carbon::parse($reserva->fecha_reserva)->format('H:i:s');

            // Formar la nueva fecha completa
            $nuevaFechaCompleta = $nuevaFechaEvento . ' ' . $horaOriginal;

            // Actualizar la reserva
            $reserva->fecha_reserva = $nuevaFechaCompleta;
            $reserva->save();

            // Guardar para notificación
            $reservasActualizadas[] = [
                'usuario' => $reserva->usuario,
                'nueva_fecha' => $nuevaFechaCompleta
            ];
        }

        // Enviar correos a los usuarios afectados
        foreach ($reservasActualizadas as $item) {
            $usuario = $item['usuario'];
            $nuevaFecha = $item['nueva_fecha'];

            if (!empty($usuario->Email)) {
                $mensaje = "Hola {$usuario->Nombre}, la fecha de tu reserva ha cambiado a {$nuevaFecha}. Gracias por tu comprensión.";
                Mail::to($usuario->Email)->send(new CorreoPersonalizado($mensaje));
            }
        }

        return response()->json([
            'message' => 'Fecha del evento y reservas actualizadas correctamente',
            'nueva_fecha_evento' => $nuevaFechaEvento,
            'total_reservas_afectadas' => count($reservasActualizadas)
        ]);
    }


    public function getReservasPorIdUsuario($id)
    {
        $usuario = \App\Models\Usuario::find($id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Cargar reservas junto con el título de la publicación
        $reservas = $usuario->reservas()->with(['publicacion:id,titulo'])->get();

        return response()->json($reservas);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar sin usuario_id
        $validated = $request->validate([
            'publicacion_id' => 'required|exists:publicaciones,id',
            'hora_reserva' => 'required|date_format:H:i', // por ejemplo '16:00'
            'total_pagar' => 'nullable|integer',
            'personas' => 'nullable|integer|min:1|max:4',
        ]);

        // Obtener el usuario autenticado
        $usuario = auth()->user();

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        // Obtener la publicación
        $publicacion = Publicacion::findOrFail($validated['publicacion_id']);

        // Formar fecha completa con fecha_evento + hora_reserva
        $fechaEvento = \Illuminate\Support\Carbon::parse($publicacion->fecha_evento)->format('Y-m-d');
        $hora = str_pad(substr($validated['hora_reserva'], 0, 2), 2, '0', STR_PAD_LEFT) . ':00:00';
        $fechaReserva = $fechaEvento . ' ' . $hora;

        // Crear reserva
        $reserva = Reserva::create([
            'usuario_id' => $usuario->id,
            'publicacion_id' => $validated['publicacion_id'],
            'fecha_reserva' => $fechaReserva,
            'total_pagar' => $validated['total_pagar'] ?? null,
            'personas' => $validated['personas'] ?? null,
        ]);

        return response()->json([
            'message' => 'Reserva creada correctamente',
            'reserva' => $reserva
        ], 201);
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
        $reserva = Reserva::with(['usuario:id,Nombre,Apellidos', 'publicacion:id,titulo'])
            ->find($id);

        if (!$reserva) {
            return response()->json(['error' => 'Reserva no encontrada'], 404);
        }

        $nombreCompleto = $reserva->usuario
            ? $reserva->usuario->Nombre . ' ' . $reserva->usuario->Apellidos
            : 'Desconocido';

        return response()->json([
            'id' => $reserva->id,
            'usuario_id' => $reserva->usuario->id ?? null,
            'publicacion_id' => $reserva->publicacion->id ?? null,
            'nombre_usuario' => $nombreCompleto,
            'titulo_publicacion' => $reserva->publicacion->titulo ?? 'Sin título',
            'fecha_reserva' => $reserva->fecha_reserva,
            'total_pagar' => $reserva->total_pagar ?? 0,
            'personas' => $reserva->personas,
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Validar los datos, incluyendo el ID de la reserva
        $validated = $request->validate([
            'id' => 'required|exists:reservas,id',
            'usuario_id' => 'required|exists:usuarios,id',
            'publicacion_id' => 'required|exists:publicaciones,id',
            'fecha_reserva' => 'required|date_format:Y-m-d H:i:s',
            'total_pagar' => 'nullable|numeric',
            'personas' => 'nullable|integer|min:1|max:4',
        ]);

        // Buscar la reserva
        $reserva = Reserva::find($validated['id']);

        if (!$reserva) {
            return response()->json(['error' => 'Reserva no encontrada'], 404);
        }

        // (Opcional) Validar que el usuario autenticado sea el propietario
        if ($reserva->usuario_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado para modificar esta reserva'], 403);
        }

        // Actualizar campos
        $reserva->usuario_id = $validated['usuario_id'];
        $reserva->publicacion_id = $validated['publicacion_id'];
        $reserva->fecha_reserva = $validated['fecha_reserva'];
        $reserva->total_pagar = $validated['total_pagar'] ?? null;
        $reserva->personas = $validated['personas'] ?? null;

        $reserva->save();

        return response()->json([
            'message' => 'Reserva actualizada correctamente',
            'reserva' => $reserva
        ]);
    }


    public function intercambiarReserva(Request $request)
    {
        // Validar los datos del request
        $validated = $request->validate([
            'id' => 'required|exists:reservas,id', // ID de la reserva que se quiere cambiar
            'nueva_fecha_reserva' => 'required|date_format:Y-m-d H:i:s', // Nueva fecha de reserva
            'correo' => 'nullable|boolean' // Si se debe enviar correo
        ]);

        // Obtener la reserva original
        $reservaOriginal = Reserva::with('usuario')->findOrFail($validated['id']);

        // Buscar una reserva para intercambiar (misma publicación y fecha nueva)
        $reservaDestino = Reserva::with('usuario')
            ->where('fecha_reserva', $validated['nueva_fecha_reserva'])
            ->where('publicacion_id', $reservaOriginal->publicacion_id)
            ->first();

        // Si no existe la reserva destino con la nueva fecha, devolver error
        if (!$reservaDestino) {
            return response()->json(['error' => 'No existe una reserva con esa fecha para intercambiar'], 404);
        }

        // Intercambiar las fechas de las reservas
        $fechaTemp = $reservaOriginal->fecha_reserva;
        $reservaOriginal->fecha_reserva = $reservaDestino->fecha_reserva;
        $reservaDestino->fecha_reserva = $fechaTemp;

        // Guardar las reservas con las nuevas fechas
        $reservaOriginal->save();
        $reservaDestino->save();

        // Si el parámetro 'correo' es true, enviar correos a los usuarios
        if ($request->has('correo') && $request->correo) {
            $usuario1 = $reservaOriginal->usuario;
            $usuario2 = $reservaDestino->usuario;

            // Verificar si ambos usuarios tienen correo
            if (!empty($usuario1->Email) && !empty($usuario2->Email)) {
                $mensaje1 = "Hola {$usuario1->Nombre}, tu reserva ha sido cambiada al horario {$reservaOriginal->fecha_reserva}.";
                $mensaje2 = "Hola {$usuario2->Nombre}, tu reserva ha sido cambiada al horario {$reservaDestino->fecha_reserva}.";

                // Enviar los correos a ambos usuarios
                Mail::to($usuario1->Email)->send(new CorreoPersonalizado($mensaje1));
                Mail::to($usuario2->Email)->send(new CorreoPersonalizado($mensaje2));
            } else {
                return response()->json(['error' => 'Uno o ambos usuarios no tienen un Email asignado.'], 422);
            }
        }

        // Respuesta exitosa
        return response()->json([
            'message' => 'Reserva intercambiada correctamente',
            'reserva_1' => $reservaOriginal,
            'reserva_2' => $reservaDestino
        ]);
    }


    public function obtenerReservasDetalladas()
    {
        $reservas = Reserva::with(['usuario:id,Nombre,Apellidos', 'publicacion:id,titulo'])
            ->get()
            ->map(function ($reserva) {
                $nombreCompleto = $reserva->usuario
                    ? $reserva->usuario->Nombre . ' ' . $reserva->usuario->Apellidos
                    : 'Desconocido';

                return [
                    'id' => $reserva->id,
                    'nombre_usuario' => $nombreCompleto,
                    'titulo_publicacion' => $reserva->publicacion->titulo ?? 'Sin título',
                    'fecha_reserva' => $reserva->fecha_reserva,
                    'estado' => Carbon::parse($reserva->fecha_reserva)->isFuture() ? 'pendiente' : 'pasada',
                    'total_pagar' => $reserva->total_pagar ?? 0,
                ];
            });

        return response()->json($reservas);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reserva = Reserva::find($id);

        if (!$reserva) {
            return response()->json(['error' => 'Reserva no encontrada'], 404);
        }

        if ($reserva->usuario_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $reserva->delete();

        return response()->json(['mensaje' => 'Reserva cancelada correctamente']);
    }
}
