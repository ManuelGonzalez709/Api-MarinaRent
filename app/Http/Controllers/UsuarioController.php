<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Validator;

class UsuarioController 
{
    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json(data: $usuarios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validación de los datos entrantes
    $validator = Validator::make(
        $request->all(),
        [
            'Nombre' => 'required|string|max:255',
            'Apellidos' => 'required|string|max:255',
            'Fecha_nacimiento' => 'required|date',
            'Email' => 'required|email|unique:usuarios,Email',
            'Password' => 'required|string|min:8',
            'Tipo' => 'required|in:usuario,admin',
        ]
    );

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Crear un nuevo usuario
    $usuario = Usuario::create([
        'Nombre' => $request->Nombre,
        'Apellidos' => $request->Apellidos,
        'Email' => $request->Email,
        'Password' => bcrypt($request->Password), // Encriptar la contraseña
        'Tipo' => $request->Tipo,
    ]);

    // Responder con el usuario creado
    return response()->json($usuario, 201);
}

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        // Buscar el usuario por ID
        $usuario = Usuario::find($id);

        // Si el usuario no existe, devolver un error 404
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Retornar el usuario en formato JSON
        return response()->json($usuario);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validación de los datos entrantes
        $validated = $request->validate([
            'Nombre' => 'sometimes|required|string|max:255',
            'Apellidos' => 'sometimes|required|string|max:255',
            'Email' => 'sometimes|required|email|unique:usuarios,Email,' . $id,
            'Password' => 'sometimes|required|string|min:8',
            'Tipo' => 'sometimes|required|in:usuario,admin',
        ]);

        // Buscar el usuario por ID
        $usuario = Usuario::find($id);

        // Si el usuario no existe, devolver un error 404
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Actualizar el usuario
        $usuario->update(array_filter($validated)); // Usamos array_filter para evitar actualizar valores nulos

        // Responder con el usuario actualizado
        return response()->json($usuario);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Buscar el usuario por ID
        $usuario = Usuario::find($id);

        // Si el usuario no existe, devolver un error 404
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Eliminar el usuario
        $usuario->delete();

        // Responder con un mensaje de éxito
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
