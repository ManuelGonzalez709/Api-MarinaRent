<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Model
{
    use HasApiTokens, Notifiable, HasFactory;
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
