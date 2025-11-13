<?php

namespace App\Http\Controllers;

use App\Models\Idioma;
use App\Models\Content;
use App\Models\Menu;
use App\Models\Configuracion;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WebController extends Controller
{
    /**
     * Página principal - redirección al idioma por defecto
     */
    public function index()
    {
        $idioma = Session::get('idioma') ?? 
                  Idioma::where('es_principal', true)
                        ->where('activo', true)
                        ->first()->etiqueta ?? 'es';
        
        return redirect("/{$idioma}");
    }

    /**
     * Página de inicio con idioma específico
     */
    public function inicio($idioma)
    {
        $idiomaNormalizado = normalizar_etiqueta_idioma($idioma) ?? 'es';
        Session::put('idioma_actual', $idiomaNormalizado);

        // Obtener configuración general
        $configuracion = Configuracion::first();
        
        // Obtener menús principales
        $menus = Menu::principal()
            ->where('visible', true)
            ->with([
                'textos.idioma',
                'content.textos.idioma',
                'children' => function ($query) {
                    $query->where('visible', true)
                        ->with([
                            'textos.idioma',
                            'content.textos.idioma',
                        ]);
                },
            ])
            ->get();
        
        // Obtener contenido de inicio
        $contenidoInicio = Content::where('actions', 'inicio')
            ->with(['textos.idioma'])
            ->first();
        
        // Obtener noticias de portada
        $noticiasPortada = Content::noticias()
                                 ->portada()
                                 ->with(['textos' => function($query) use ($idiomaNormalizado) {
                                     $query->byIdioma($idiomaNormalizado)->visible();
                                 }])
                                 ->orderBy('fecha_publicacion', 'desc')
                                 ->limit(6)
                                 ->get();


        // Obtener slides activos
        $slides = Slide::query()
            ->visible()
            ->ordered()
            ->with(['translations.idioma'])
            ->get();

        // Galerías activas para el carrusel
        $galerias = \App\Models\Gallery::activas()
            ->with(['images' => function($q) {
                $q->active();
            }])
            ->orderBy('id', 'desc')
            ->limit(1)
            ->get();
        
        return view('web.inicio', compact(
            'configuracion', 
            'menus', 
            'contenidoInicio', 
            'noticiasPortada',
            'slides',
            'galerias'
        ));
    }

    /**
     * Página de contenido dinámico
     */
    public function contenido($idioma, $slug)
    {
        // Buscar el contenido por slug
        $idiomaNormalizado = normalizar_etiqueta_idioma($idioma) ?? 'es';

        $contenido = Content::whereHas('textos', function($query) use ($slug, $idiomaNormalizado) {
            $query->bySlug($slug)->byIdioma($idiomaNormalizado)->visible();
        })->with(['textos' => function($query) use ($idiomaNormalizado) {
            $query->byIdioma($idiomaNormalizado)->visible();
        }, 'galeria'])->firstOrFail();
        
        $configuracion = Configuracion::first();
        $menus = Menu::principal()
            ->where('visible', true)
            ->with([
                'textos.idioma',
                'content.textos.idioma',
                'children' => function ($query) {
                    $query->where('visible', true)
                        ->with([
                            'textos.idioma',
                            'content.textos.idioma',
                        ]);
                },
            ])
            ->get();
        
        return view('web.contenido', compact('contenido', 'configuracion', 'menus'));
    }

    /**
     * Listado de noticias
     */
    public function noticias($idioma)
    {
        $idiomaNormalizado = normalizar_etiqueta_idioma($idioma) ?? 'es';

        $noticias = Content::noticias()
                          ->with(['textos' => function($query) use ($idiomaNormalizado) {
                              $query->byIdioma($idiomaNormalizado)->visible();
                          }])
                          ->orderBy('fecha_publicacion', 'desc')
                          ->paginate(10);
        
        $configuracion = Configuracion::first();
        $menus = Menu::principal()
            ->where('visible', true)
            ->with([
                'textos.idioma',
                'content.textos.idioma',
                'children' => function ($query) {
                    $query->where('visible', true)
                        ->with([
                            'textos.idioma',
                            'content.textos.idioma',
                        ]);
                },
            ])
            ->get();
        
        return view('web.noticias', compact('noticias', 'configuracion', 'menus'));
    }

    /**
     * Cambiar idioma
     */
    public function cambiarIdioma($idioma)
    {
        $idiomaModel = Idioma::where('codigo', $idioma)
                            ->where('activado', true)
                            ->first();
        
        if ($idiomaModel) {
            Session::put('idioma', $idioma);
            Session::put('idioma_id', $idiomaModel->id);
        }
        
        return redirect()->back();
    }
}
