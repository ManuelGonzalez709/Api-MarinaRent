<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        h2 {
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center">Restablecer Contraseña</h2>
            <form method="POST" action="{{ route('restablecer.password') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-3">
                    <label for="password" class="form-label">Nueva Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Restablecer Contraseña</button>
            </form>
        </div>
    </div>
</body>
</html>
