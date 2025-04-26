<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Publicacion>
 */
class PublicacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'Titulo' => $this->faker->sentence,  
            'Descripcion' => $this->faker->paragraph,  
            'Fecha_evento' => $this->faker->date,
            'Tipo' => $this->faker->randomElement(['alquilable','informativo']),  
            'Precio' => $this->faker->numberBetween(100, 10000), 
            'Imagen' => $this->faker->imageUrl(640, 480, 'business', true),  
            'Aforo_maximo' => $this->faker->numberBetween(100, 500), 
        ];
    }
}
