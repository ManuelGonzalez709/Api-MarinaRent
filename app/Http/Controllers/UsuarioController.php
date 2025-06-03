<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\CorreoPersonalizado;

/*
|--------------------------------------------------------------------------
| UsuarioController
|--------------------------------------------------------------------------
| Controlador para la gestión de usuarios.
| Cada método indica su endpoint correspondiente.
*/

class UsuarioController extends Controller
{
    /**
     * GET /usuarios
     * Devuelve todos los usuarios.
     */
    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json(data: $usuarios);
    }

    /**
     * POST /paginarUsuarios
     * Devuelve usuarios paginados. Recibe 'pagina' en el body.
     */
    public function paginarUsuarios(Request $request)
    {
        // Número de elementos por página
        $perPage = 10;

        // Página solicitada (por defecto 1)
        $page = (int) $request->input('pagina', 1);
        $page = max($page, 1);

        // Total de usuarios y páginas
        $total = Usuario::count();
        $totalPages = (int) ceil($total / $perPage);

        // Usuarios de la página solicitada
        $usuarios = Usuario::skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($usuario) {
                return [
                    'id' => $usuario->id,
                    'Nombre' => $usuario->Nombre,
                    'Apellidos' => $usuario->Apellidos,
                    'Email' => $usuario->Email,
                    'Fecha_nacimiento' => $usuario->Fecha_nacimiento,
                    'Tipo' => $usuario->Tipo,
                    'updated_at' => $usuario->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $usuarios,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    /**
     * GET /isAdmin
     * Devuelve si el usuario autenticado es admin.
     */
    public function obtenerAdmin()
    {
        $usuario = auth()->user();

        return response()->json([
            'is_admin' => $usuario->Tipo === 'admin'
        ]);
    }

    /**
     * GET /usuario/getId
     * Devuelve solo el id del usuario autenticado.
     */
    public function obtenerUsuarioAutenticado()
    {
        $usuario = auth()->user();
        return response()->json($usuario->only(['id']));
    }

    /**
     * POST /usuarios
     * Crea un nuevo usuario.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
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

        // Crear el usuario
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
     * GET /getData
     * Devuelve los datos del usuario autenticado.
     */
    public function obtenerDatosUsuarioAutenticado()
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        return response()->json([
            'id' => $usuario->id,
            'Nombre' => $usuario->Nombre,
            'Apellidos' => $usuario->Apellidos,
            'Email' => $usuario->Email,
            'Fecha_nacimiento' => $usuario->Fecha_nacimiento,
            'Tipo' => $usuario->Tipo,
            "updated_at" => $usuario->updated_at,
            "created_at" => $usuario->created_at,
        ]);
    }

    /**
     * POST /mailTo
     * Envía un correo personalizado al usuario.
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
     * GET /usuarios/{id}
     * Devuelve los datos de un usuario por su id.
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
     * PUT /usuarios/actualizar
     * Actualiza los datos de un usuario.
     */
    public function actualizar(Request $request)
    {
        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id',
            'Nombre' => 'required|string|max:255',
            'Apellidos' => 'required|string|max:255',
            'Email' => 'required|email|max:255|unique:usuarios,Email,' . $request->input('id_usuario'),
            'Fecha_nacimiento' => 'required|date',
            'Tipo' => 'required|string|in:admin,usuario',
            'Password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Buscar usuario y actualizar campos
        $usuario = Usuario::find($request->input('id_usuario'));

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $usuario->Nombre = $request->input('Nombre');
        $usuario->Apellidos = $request->input('Apellidos');
        $usuario->Email = $request->input('Email');
        $usuario->Fecha_nacimiento = $request->input('Fecha_nacimiento');
        $usuario->Tipo = $request->input('Tipo');

        // Actualizar contraseña si se proporciona
        if ($request->has('Password') && $request->input('Password') !== null) {
            $usuario->Password = bcrypt($request->input('Password'));
        }

        $usuario->save();

        return response()->json(['usuario' => $usuario], 200);
    }

    /**
     * DELETE /usuarios/{id}
     * Elimina un usuario por su id.
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

