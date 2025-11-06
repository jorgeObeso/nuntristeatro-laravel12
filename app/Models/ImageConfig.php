<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_contenido',
        'tipo_imagen',
        'ancho',
        'alto',
        'mantener_aspecto',
        'formato',
        'calidad',
        'redimensionar',
        'activo',
    ];

    protected $casts = [
        'mantener_aspecto' => 'boolean',
        'redimensionar' => 'boolean',
        'activo' => 'boolean',
        'ancho' => 'integer',
        'alto' => 'integer',
        'calidad' => 'integer',
    ];

    /**
     * Obtener configuración para un tipo específico de contenido e imagen
     */
    public static function getConfig($tipoContenido, $tipoImagen)
    {
        return self::where('tipo_contenido', $tipoContenido)
                   ->where('tipo_imagen', $tipoImagen)
                   ->where('activo', true)
                   ->first();
    }

    /**
     * Obtener todas las configuraciones activas
     */
    public static function getActiveConfigs()
    {
        return self::where('activo', true)
                   ->orderBy('tipo_contenido')
                   ->orderBy('tipo_imagen')
                   ->get()
                   ->groupBy('tipo_contenido');
    }
}