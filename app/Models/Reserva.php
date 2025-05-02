<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;
    protected $table = 'reservas';

    protected $fillable = [
        'usuario_id',
        'publicacion_id',
        'fecha_reserva',
        'total_pagar',
        'personas',
    ];


    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class);
    }
    
}
