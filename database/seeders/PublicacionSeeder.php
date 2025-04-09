<?php

namespace Database\Seeders;

use App\Models\Publicacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublicacionSeeder extends Seeder
{
    public function run()
    {
        // Crear 10 publicaciones
        Publicacion::factory(10)->create();
    }
}