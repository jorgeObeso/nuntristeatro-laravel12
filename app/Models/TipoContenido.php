<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoContenido extends Model
{
    use HasFactory;

    protected $table = 'tipo_contenidos';

    protected $fillable = [
        'tipo_contenido',
        'icono',
    ];

    /**
     * Relación: Un tipo de contenido puede tener muchos textos
     */
    public function textos()
    {
        return $this->hasMany(TextoIdioma::class);
    }
}