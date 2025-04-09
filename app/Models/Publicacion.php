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
        'Fecha_publicacion',
        "Tipo",
        "Precio",
        "Imagen"
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}