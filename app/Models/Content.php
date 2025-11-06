<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $table = 'contents';

    protected $fillable = [
        'lugar',
        'fecha',
        'fecha_publicacion',
        'tipo_contenido',
        'imagen',
        'imagen_alt',
        'imagen_portada',
        'imagen_portada_alt',
        'pagina_estatica',
        'columnas',
        'fb_pixel',
        'portada',
        'galeria_id',
        'actions',
        'orden',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_publicacion' => 'date',
        'pagina_estatica' => 'boolean',
        'portada' => 'boolean',
    ];

    /**
     * Relación: Un contenido pertenece a una galería
     */
    public function galeria()
    {
        return $this->belongsTo(Galeria::class);
    }

    /**
     * Relación: Un contenido puede tener muchos textos en diferentes idiomas
     */
    public function textos()
    {
        return $this->hasMany(TextoIdioma::class, 'contenido_id');
    }

    /**
     * Relación: Un contenido puede tener muchos menús
     */
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Scope para contenidos de portada
     */
    public function scopePortada($query)
    {
        return $query->where('portada', true);
    }

    /**
     * Scope para páginas estáticas
     */
    public function scopePaginaEstatica($query)
    {
        return $query->where('pagina_estatica', true);
    }

    /**
     * Scope para noticias
     */
    public function scopeNoticias($query)
    {
        return $query->where('tipo_contenido', 'noticia');
    }

    /**
     * Obtener el texto en un idioma específico
     */
    public function getTextoEnIdioma($idiomaId)
    {
        return $this->textos()->where('idioma_id', $idiomaId)->first();
    }
}