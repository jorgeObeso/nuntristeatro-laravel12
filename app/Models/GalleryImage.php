<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryImage extends Model
{
    protected $fillable = [
        'gallery_id',
        'titulo',
        'descripcion', 
        'imagen',
        'imagen_miniatura',
        'alt_text',
        'orden',
        'activa',
        'metadatos'
    ];

    protected $casts = [
        'activa' => 'boolean',
        'metadatos' => 'array'
    ];

    /**
     * Relación con la galería
     */
    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    /**
     * Scope para imágenes activas
     */
    public function scopeActive($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Obtener URL de la imagen con sistema responsive
     */
    public function getImagenUrlAttribute(): string
    {
        return responsive_image($this->imagen, false); // Versión desktop
    }

    /**
     * Obtener URL de la imagen móvil con sistema responsive
     */
    public function getImagenMobileUrlAttribute(): string
    {
        return responsive_image($this->imagen, true); // Versión móvil
    }

    /**
     * Obtener URL de la miniatura
     */
    public function getMiniaturaUrlAttribute(): ?string
    {
        if ($this->imagen_miniatura) {
            return responsive_image($this->imagen_miniatura);
        }
        
        // Fallback a la imagen principal
        return $this->imagen_url;
    }

    /**
     * Generar HTML responsive para la imagen
     */
    public function getResponsiveHtmlAttribute(): string
    {
        return responsive_image_html(
            $this->imagen,
            $this->alt_text ?: $this->titulo ?: 'Imagen de galería',
            'gallery-image',
            ''
        );
    }

    /**
     * Obtener texto alt apropiado
     */
    public function getAltTextAttribute($value): string
    {
        return $value ?: $this->titulo ?: 'Imagen de galería ' . $this->gallery->nombre;
    }
}
