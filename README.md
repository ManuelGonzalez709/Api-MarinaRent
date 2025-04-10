# 🚀 API Proyecto Final MarinaRent

La API **MarinaRent** es un proyecto desarrollado en **Laravel** para gestionar el alquiler de amarres y servicios en un puerto deportivo. Esta API está diseñada para gestionar usuarios, reservas, y servicios relacionados con los amarres.

![Laravel](https://img.shields.io/badge/Laravel-v8.x-brightgreen)

## 🔧 Requisitos

- **PHP 8.0+**
- **Composer**
- **Base de datos (MySQL, SQLite, etc.)**
- **Laravel 8.x o superior**

## 📥 Instalación

### 1. Clonar el repositorio

Clona este repositorio en tu máquina local:

```bash
git clone https://github.com/ManuelGonzalez709/Api-MarinaRent.git

---

## 🧪 Seeders

El proyecto incluye **seeders automáticos** que generan datos de prueba (como usuarios, amarres, reservas, etc.) al ejecutar las migraciones. Esto facilita la prueba de la API sin necesidad de insertar registros manualmente.

```bash
php artisan migrate:fresh --seed
