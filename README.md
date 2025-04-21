# 🚀 API Proyecto Final MarinaRent

La API **MarinaRent** es un proyecto desarrollado en **Laravel** para gestionar el alquiler de amarres y servicios en un puerto deportivo. Está diseñada para manejar usuarios, reservas, servicios y más, todo ello con seguridad, autenticación por tokens y datos de prueba generados automáticamente.

![Laravel](https://img.shields.io/badge/Laravel-v8.x-brightgreen)

---

## 🔐 Seguridad

Esta API implementa autenticación mediante tokens gracias a SANCTUM, permitiendo que solo usuarios registrados o autenticados puedan acceder a la mayoría de endpoints.

- Para obtener acceso, el usuario debe registrarse o iniciar sesión.
- Se genera un **token de acceso** que debe ser enviado en cada petición protegida mediante el header
- Permite el **Restablecimiento de Contraseñas** mediante un enlace enviado al correo

## 🧪 Seeders
El proyecto incluye seeders automáticos para generar datos falsos de prueba (usuarios, amarres, reservas, etc.). Esto facilita la prueba sin necesidad de introducir datos manualmente.

php artisan migrate:fresh --seed

---

### CRUD Operations
- `GET /api/users` → List users
- `POST /api/users` → Create user
- `PUT /api/users/{id}` → Update user
- `DELETE /api/users/{id}` → Delete user
- `GET /api/reservations` → List reservations
- `POST /api/reservations` → Create reservation
- `PUT /api/reservations/{id}` → Update reservation
- `DELETE /api/reservations/{id}` → Delete reservation
- `GET /api/publications` → List publications
- `POST /api/publications` → Create publication
- `PUT /api/publications/{id}` → Update publication
- `DELETE /api/publications/{id}` → Delete publication
- `POST /api/upload` → Upload an image (multipart/form-data). This route allows you to upload images related to publications or any other   entities that require images in your application.

### Headers for protected routes
```http
Authorization: Bearer {YOUR_TOKEN}
