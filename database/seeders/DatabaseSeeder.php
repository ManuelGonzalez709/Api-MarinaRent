<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Llamar a los seeders de Usuario, Publicación y Reserva
        $this->call([
            UsuarioSeeder::class,  // Seeder de Usuario
            PublicacionSeeder::class,  // Seeder de Publicación
            ReservaSeeder::class,  // Seeder de Reserva
        ]);
    }
}
