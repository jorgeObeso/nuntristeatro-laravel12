<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    protected $fillable = [
        'parent_id',
        'title',
        'label',
        'content_id',
        'url',
        'order',
        'icon',
        'menu_pie',
    ];

    protected $casts = [
        'menu_pie' => 'boolean',
    ];

    /**
     * Relación: Un menú puede tener un padre (menú padre)
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Relación: Un menú puede tener muchos hijos (submenús)
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    /**
     * Relación: Un menú puede estar asociado a un contenido
     */
    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * Scope para menús principales (sin padre)
     */
    public function scopePrincipal($query)
    {
        return $query->whereNull('parent_id')->orderBy('order');
    }

    /**
     * Scope para menús de pie
     */
    public function scopeMenuPie($query)
    {
        return $query->where('menu_pie', true)->orderBy('order');
    }
}