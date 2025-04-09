<?php

namespace Database\Seeders;

use App\Models\Reserva;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReservaSeeder extends Seeder
{
    public function run()
    {
        // Crear 10 reservas, cada una relacionada con un usuario y una publicación
        Reserva::factory(10)->create();
    }
}
