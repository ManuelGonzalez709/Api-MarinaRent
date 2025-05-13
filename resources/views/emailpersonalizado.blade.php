<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Correo Personalizado</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f9fafb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div style="max-width: 600px; margin: 40px auto; background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 15px rgba(0,0,0,0.1);">
        <div style="background-color: #1d4ed8; padding: 20px 30px;">
            <h2 style="color: white; font-size: 24px; margin: 0;">📬 Mensaje desde Marina Rent</h2>
        </div>

        <div style="padding: 30px;">
            <p style="font-size: 16px; color: #111827; margin-bottom: 20px;">
                Hola 👋,
            </p>

            <p style="font-size: 16px; color: #374151; margin-bottom: 30px;">
                {{ $mensaje }}
            </p>
            
        </div>

        <div style="background-color: #f3f4f6; padding: 20px 30px; text-align: center; font-size: 12px; color: #6b7280;">
            Este mensaje fue generado automáticamente por el sistema de Marina Rent.
        </div>
    </div>
</body>
</html>
