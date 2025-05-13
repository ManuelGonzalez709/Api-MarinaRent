<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\CorreoPersonalizado;
class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json(data: $usuarios);
    }

    public function obtenerAdmin()
    {
        $usuario = auth()->user();

        return response()->json([
            'is_admin' => $usuario->Tipo === 'admin'
        ]);
    }

    public function obtenerUsuarioAutenticado()
    {
        $usuario = auth()->user();
        return response()->json($usuario->only(['id']));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos
        $validator = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:255',
            'Apellidos' => 'required|string|max:255',
            'Email' => 'required|email|max:255|unique:usuarios,Email',
            'Fecha_nacimiento' => 'required|date',
            'Tipo' => 'required|string|in:admin,usuario',
            'Password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear el nuevo usuario
        $usuario = Usuario::create([
            'Nombre' => $request->input('Nombre'),
            'Apellidos' => $request->input('Apellidos'),
            'Email' => $request->input('Email'),
            'Fecha_nacimiento' => $request->input('Fecha_nacimiento'),
            'Tipo' => $request->input('Tipo'),
            'Password' => bcrypt($request->input('Password')),
        ]);

        return response()->json(['usuario' => $usuario], 201);
    }
    /**
     * Envia un correo electrónico personalizado al usuario.
     * @param \Illuminate\Http\Request $request
     */
    

    public function enviarCorreoPersonalizado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'mensaje' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Datos inválidos',
                'messages' => $validator->errors()
            ], 422);
        }

        Mail::to($request->email)->send(new CorreoPersonalizado($request->mensaje));

        return response()->json(['success' => 'Correo enviado correctamente con vista personalizada'], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        return response()->json([
            'id' => $usuario->id,
            'Nombre' => $usuario->Nombre,
            'Apellidos' => $usuario->Apellidos,
            'Email' => $usuario->Email,
            'Fecha_nacimiento' => $usuario->Fecha_nacimiento,
            'Tipo' => $usuario->Tipo,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function actualizar(Request $request)
    {
        // Validar los datos que vienen en la solicitud
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id', // Validamos que el ID exista en la base de datos
            'Nombre' => 'required|string|max:255',
            'Apellidos' => 'required|string|max:255',
            'Email' => 'required|email|max:255|unique:usuarios,Email,' . $request->input('id_usuario'),
            'Fecha_nacimiento' => 'required|date',
            'Tipo' => 'required|string|in:admin,usuario',
            'Password' => 'nullable|string|min:8', // Si no se proporciona una nueva contraseña, no se actualizará
        ]);

        // Si hay errores en la validación, devolver una respuesta con los errores
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Buscar al usuario por su ID recibido en el cuerpo de la solicitud
        $usuario = Usuario::find($request->input('id_usuario'));

        // Si el usuario no existe, devolver un error
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Actualizar los campos del usuario
        $usuario->Nombre = $request->input('Nombre');
        $usuario->Apellidos = $request->input('Apellidos');
        $usuario->Email = $request->input('Email');
        $usuario->Fecha_nacimiento = $request->input('Fecha_nacimiento');
        $usuario->Tipo = $request->input('Tipo');

        // Si se proporciona una nueva contraseña, actualizarla
        if ($request->has('Password') && $request->input('Password') !== null) {
            $usuario->Password = bcrypt($request->input('Password')); // Encriptar la nueva contraseña
        }

        // Guardar los cambios en la base de datos
        $usuario->save();

        // Devolver una respuesta con el usuario actualizado
        return response()->json(['usuario' => $usuario], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }

}

