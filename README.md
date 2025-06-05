# 🚤 API MarinaRent - Proyecto Final DAW

[![Laravel](https://img.shields.io/badge/Laravel-v8.x-brightgreen)](https://laravel.com/)
[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

Bienvenido a **MarinaRent**, la API definitiva para gestionar el alquiler de amarres y servicios en un puerto deportivo, desarrollada con **Laravel** como proyecto final de DAW. Este backend está pensado para ser robusto, seguro y fácil de probar, integrando autenticación, seeders automáticos y una arquitectura clara de endpoints para usuarios, reservas, publicaciones y administración.

---

## 🛡️ Seguridad & Autenticación

La API protege todos los endpoints sensibles usando **Laravel Sanctum**:

- Registro y login con generación de **token de acceso**.
- El token debe enviarse en el header en cada petición protegida:
  ```http
  Authorization: Bearer TU_TOKEN
  ```
- Reestablecimiento de contraseña vía email.
- Acceso restringido a usuarios autenticados y roles de administrador.

### Ejemplo de autenticación

**Registro:**
```http
POST /api/register
Content-Type: application/json

{
  "name": "Manuel",
  "email": "manuel@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

**Login:**
```http
POST /api/login
Content-Type: application/json

{
  "email": "manuel@example.com",
  "password": "password"
}
```

**Respuesta:**
```json
{
  "token": "TU_TOKEN_DE_ACCESO"
}
```

---

## ⚡ Endpoints principales

### Usuarios
- `GET    /api/usuarios`           → Lista usuarios
- `POST   /api/usuarios`           → Crear usuario
- `PUT    /api/usuarios/{id}`      → Actualizar usuario

### Reservas
- `GET    /api/reservas`           → Lista reservas
- `POST   /api/reservas`           → Crear reserva
- `DELETE /api/reservas/{id}`      → Eliminar reserva
- `GET    /api/obtenerReservasUsuario` → Reservas del usuario autenticado
- `GET    /api/obtenerReservasUsuario/{id}` → Reservas por ID de usuario
- `POST   /api/disponibilidadReserva` → Comprobar disponibilidad de hora y fecha
- `POST   /api/capacidadDisponible`   → Consultar aforo/capacidad de publicación

### Publicaciones
- `GET    /api/publicaciones`      → Lista de publicaciones
- `GET    /api/informativos`       → Publicaciones informativas
- `GET    /api/alquilables`        → Publicaciones disponibles para alquilar
- `POST   /api/publicaciones`      → Crear publicación (admin)
- `POST   /api/upload`             → Subir imagen (multipart/form-data)

### Admin
- `POST   /api/actualizar`                     → Actualizar publicación (admin)
- `POST   /api/actualizarReservas`             → Actualizar reservas masivamente (admin)
- `POST   /api/actualizarFechaPublicacion`     → Cambiar fecha de evento y reservas (admin)
- `POST   /api/intercambiarFechas`             → Intercambiar reservas (admin)
- `GET    /api/isAdmin`                        → Verifica si el usuario autenticado es admin

### Ejemplo de subida de imágenes

```http
POST /api/upload
Content-Type: multipart/form-data
Authorization: Bearer TU_TOKEN

Body:
photo: archivo.jpg
```
**Respuesta:**
```json
{
  "success": true,
  "path": "http://localhost:8000/storage/photos/archivo.jpg"
}
```

---

## 🧪 Seeders y Pruebas de Datos

¡Olvídate de meter datos a mano! El proyecto incluye seeders automáticos para crear usuarios, amarres, reservas y publicaciones de prueba:

```bash
php artisan migrate:fresh --seed
```

---

## ⚙️ Requisitos

- **PHP 8.0+**
- **Composer**
- **Laravel 8.x o superior**
- **Base de datos (MySQL, SQLite, etc.)**

---

## 🚀 Instalación y Puesta en marcha

### 1. Clona el repositorio
```bash
git clone https://github.com/ManuelGonzalez709/Api-MarinaRent.git
cd Api-MarinaRent
```

### 2. Instala dependencias
```bash
composer install
```

### 3. Configura el entorno
```bash
cp .env.example .env
php artisan key:generate
```
Edita `.env` y ajusta tu base de datos.

### 4. Migra y pobla la base de datos
```bash
php artisan migrate --seed
```

### 5. Enlaza almacenamiento público
```bash
php artisan storage:link
```

### 6. Arranca el servidor
```bash
php artisan serve
```

---

## 👨‍💻 Autor

**Manuel González Pérez**  
🎓 Técnico Superior en DAM y DAW  
💻 Tecnologías: PHP, Laravel, Spring, React, JavaScript, .NET, Python, Java  
🔗 [GitHub](https://github.com/ManuelGonzalez709)

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT.
