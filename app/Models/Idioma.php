<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Idioma extends Model
{
    use HasFactory;

    protected $table = 'idiomas';

    protected $fillable = [
        'idioma',
        'codigo',
        'label',
        'imagen',
        'principal',
        'activado',
    ];

    protected $casts = [
        'principal' => 'boolean',
        'activado' => 'boolean',
    ];

    /**
     * Relación: Un idioma puede tener muchos textos
     */
    public function textos()
    {
        return $this->hasMany(TextoIdioma::class);
    }

    /**
     * Scope para obtener el idioma principal
     */
    public function scopePrincipal($query)
    {
        return $query->where('principal', true);
    }

    /**
     * Scope para obtener idiomas activos
     */
    public function scopeActivado($query)
    {
        return $query->where('activado', true);
    }
}