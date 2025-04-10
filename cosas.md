
# 🚀 API Proyecto Final MarinaRent

La API **MarinaRent** es un proyecto desarrollado en **Laravel** para gestionar el alquiler de amarres y servicios en un puerto deportivo. Está diseñada para manejar usuarios, reservas, servicios y más, todo ello con seguridad, autenticación por tokens y datos de prueba generados automáticamente.

![Laravel](https://img.shields.io/badge/Laravel-v8.x-brightgreen)

---

## 🔐 Seguridad

Esta API implementa autenticación mediante tokens, permitiendo que solo usuarios registrados o autenticados puedan acceder a la mayoría de endpoints.

- Para obtener acceso, el usuario debe registrarse o iniciar sesión.
- Se genera un **token de acceso** que debe ser enviado en cada petición protegida mediante el header:

```http
Authorization: Bearer TU_TOKEN_AQUI
```

---

## 🧪 Seeders

El proyecto incluye **seeders automáticos** para generar datos falsos de prueba (usuarios, amarres, reservas, etc.). Esto facilita la prueba sin necesidad de introducir datos manualmente.

```bash
php artisan migrate:fresh --seed
```

---

## 🔧 Requisitos

- **PHP 8.0+**
- **Composer**
- **Laravel 8.x o superior**
- **Base de datos (MySQL, SQLite, etc.)**

---

## 📥 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/ManuelGonzalez709/Api-MarinaRent.git
cd Api-MarinaRent
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar el entorno

```bash
cp .env.example .env
php artisan key:generate
```

Edita el archivo `.env` y configura tu conexión a la base de datos.

### 4. Migrar y poblar la base de datos

```bash
php artisan migrate --seed
```

### 5. Crear enlace simbólico para almacenamiento público

```bash
php artisan storage:link
```

### 6. Iniciar el servidor

```bash
php artisan serve
```

---

## 📡 Uso de la API

### 🔑 Autenticación

#### Registro

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

#### Login

```http
POST /api/login
Content-Type: application/json

{
  "email": "manuel@example.com",
  "password": "password"
}
```

**Respuesta esperada:**

```json
{
  "token": "TU_TOKEN_DE_ACCESO"
}
```

---

### 🧾 Endpoints protegidos

Recuerda añadir el token en cada petición:

```
Authorization: Bearer TU_TOKEN
```

#### 📤 Subida de imágenes

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

## 👨‍💻 Autor

**Manuel González Pérez**  
🎓 Técnico Superior en DAM y DAW  
💻 Tecnologías: PHP, Laravel, Spring, React, JavaScript, .NET, Python, Java  
🔗 [GitHub](https://github.com/ManuelGonzalez709)

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT.
