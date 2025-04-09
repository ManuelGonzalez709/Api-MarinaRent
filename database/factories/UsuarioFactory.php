<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition()
    {
        return [
            'Nombre' => $this->faker->firstName,  // Nombre del usuario
            'Apellidos' => $this->faker->lastName,  // Apellidos del usuario
            'Fecha_nacimiento' => $this->faker->date,  // Fecha de nacimiento (formato yyyy-mm-dd)
            'Email' => $this->faker->unique()->safeEmail,  // Correo electrónico único
            'Tipo' => $this->faker->randomElement(['admin', 'usuario']),  // Tipo de usuario (admin o usuario)
            'Password' => bcrypt('password'),  // Contraseña encriptada
        ];
    }
}
