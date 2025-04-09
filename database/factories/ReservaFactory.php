<?php

namespace Database\Factories;

use App\Models\Publicacion;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reserva>
 */
class ReservaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'usuario_id' => Usuario::factory(),  // Relacionamos con un usuario aleatorio
            'publicacion_id' => Publicacion::factory(),  // Relacionamos con una publicación aleatoria
            'fecha_reserva' => $this->faker->dateTimeBetween('now', '+1 year'),  // Fecha de reserva en el futuro, hasta un año
        ];
    }
}
