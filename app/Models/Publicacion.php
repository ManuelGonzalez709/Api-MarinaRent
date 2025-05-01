<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    use HasFactory;
    protected $table = 'publicaciones';
    protected $fillable = [
        'Titulo',
        'Descripcion',
        'Fecha_evento',
        "Tipo",
        "Precio",
        "Imagen",
        "Aforo_maximo",
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}