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


``` bash
git clone https://github.com/ManuelGonzalez709/Api-MarinaRent.git


