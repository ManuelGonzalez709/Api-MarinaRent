<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
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
