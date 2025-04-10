# 🚀 API Proyecto Final MarinaRent

La API **MarinaRent** es un proyecto desarrollado en **Laravel** para gestionar el alquiler de amarres y servicios en un puerto deportivo. Está diseñada para manejar usuarios, reservas, servicios y más, todo ello con seguridad, autenticación por tokens y datos de prueba generados automáticamente.

![Laravel](https://img.shields.io/badge/Laravel-v8.x-brightgreen)

---

## 🔐 Seguridad

Esta API implementa autenticación mediante tokens, permitiendo que solo usuarios registrados o autenticados puedan acceder a la mayoría de endpoints.

- Para obtener acceso, el usuario debe registrarse o iniciar sesión.
- Se genera un **token de acceso** que debe ser enviado en cada petición protegida mediante el header:
- 
## 🧪 Seeders
El proyecto incluye seeders automáticos para generar datos falsos de prueba (usuarios, amarres, reservas, etc.). Esto facilita la prueba sin necesidad de introducir datos manualmente.

php artisan migrate:fresh --seed

---

### CRUD Operations
- `GET /api/users` → List users
- `POST /api/users` → Create user
- `GET /api/reservations` → List reservations
- `POST /api/reservations` → Create reservation
- `GET /api/publications` → List publications
- `POST /api/publications` → Create publication

### Upload
- `POST /api/upload` → Upload an image (multipart/form-data)

### Headers for protected routes
```http
Authorization: Bearer {YOUR_TOKEN}



