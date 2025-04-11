<!-- resources/views/restablecer.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
</head>
<body>
    <h2>Restablecer Contraseña</h2>

    <!-- Mostrar los errores de validación si existen -->
  

    <form action="{{ route('restablecer.password') }}" method="POST">
        @csrf
        <!-- Campo oculto para el email -->
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Campo para la nueva contraseña -->
        <label for="password">Nueva Contraseña</label>
        <input type="password" id="password" name="password" required>
        <br>

        <!-- Confirmación de la nueva contraseña -->
        <label for="password_confirmation">Confirmar Nueva Contraseña</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>
        <br>

        <button type="submit">Aplicar Cambios</button>
    </form>
</body>
</html>
