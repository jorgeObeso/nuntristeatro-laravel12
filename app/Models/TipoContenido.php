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
     * Accessor para nombre (compatibilidad)
     */
    public function getNombreAttribute()
    {
        return $this->tipo_contenido;
    }

    /**
     * Obtener solo tipos de contenido que deben aparecer en menús públicos
     */
    public static function tiposParaMenu()
    {
        return self::whereIn('tipo_contenido', [
            'Páginas',       // Páginas generales (antes "Contenido")
            'Noticias',      // Noticias
            'Entrevistas'    // Entrevistas
        ])->get();
    }

    /**
     * Relación: Un tipo de contenido puede tener muchos textos
     */
    public function textos()
    {
        return $this->hasMany(TextoIdioma::class);
    }
}