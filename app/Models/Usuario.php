<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{

    protected $table = 'usuarios';

    protected $fillable = [
        'Nombre',
        'Apellidos',
        'Fecha_nacimiento',
        'Email',
        'Tipo',
        'Password'
    ];

     public function publicaciones()
     {
         return $this->hasMany(Publicacion::class);
     }
 
     // Relación uno a muchos con reservas
     public function reservas()
     {
         return $this->hasMany(Reserva::class);
     }
}
