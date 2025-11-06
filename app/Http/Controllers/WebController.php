<?php

namespace App\Http\Controllers;

use App\Models\Idioma;
use App\Models\Content;
use App\Models\Menu;
use App\Models\Configuracion;
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
                  Idioma::where('principal', true)
                        ->where('activado', true)
                        ->first()->codigo ?? 'es';
        
        return redirect("/{$idioma}");
    }

    /**
     * Página de inicio con idioma específico
     */
    public function inicio($idioma)
    {
        // Obtener configuración general
        $configuracion = Configuracion::first();
        
        // Obtener menús principales
        $menus = Menu::principal()->with('children')->get();
        
        // Obtener contenido de inicio
        $contenidoInicio = Content::where('actions', 'inicio')->first();
        
        // Obtener noticias de portada
        $noticiasPortada = Content::noticias()
                                 ->portada()
                                 ->with(['textos' => function($query) use ($idioma) {
                                     $query->byIdioma($idioma)->visible();
                                 }])
                                 ->orderBy('fecha_publicacion', 'desc')
                                 ->limit(6)
                                 ->get();
        
        return view('web.inicio', compact(
            'configuracion', 
            'menus', 
            'contenidoInicio', 
            'noticiasPortada'
        ));
    }

    /**
     * Página de contenido dinámico
     */
    public function contenido($idioma, $slug)
    {
        // Buscar el contenido por slug
        $contenido = Content::whereHas('textos', function($query) use ($slug, $idioma) {
            $query->bySlug($slug)->byIdioma($idioma)->visible();
        })->with(['textos' => function($query) use ($idioma) {
            $query->byIdioma($idioma)->visible();
        }, 'galeria'])->firstOrFail();
        
        $configuracion = Configuracion::first();
        $menus = Menu::principal()->with('children')->get();
        
        return view('web.contenido', compact('contenido', 'configuracion', 'menus'));
    }

    /**
     * Listado de noticias
     */
    public function noticias($idioma)
    {
        $noticias = Content::noticias()
                          ->with(['textos' => function($query) use ($idioma) {
                              $query->byIdioma($idioma)->visible();
                          }])
                          ->orderBy('fecha_publicacion', 'desc')
                          ->paginate(10);
        
        $configuracion = Configuracion::first();
        $menus = Menu::principal()->with('children')->get();
        
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
