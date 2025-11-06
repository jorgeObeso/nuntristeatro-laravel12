<?php

namespace App\Http\Middleware;

use App\Models\Idioma;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Obtener el idioma de la URL
            $locale = $request->segment(1);
            
            // Validar que el locale sea una cadena válida
            if (!is_string($locale) || empty($locale)) {
                $locale = null;
            }
            
            // Verificar si el idioma existe en la base de datos
            $idioma = null;
            if ($locale) {
                $idioma = Idioma::where('codigo', $locale)
                                ->where('activado', true)
                                ->first();
            }
            
            if ($idioma) {
                // Establecer el idioma en la aplicación y la sesión
                App::setLocale($locale);
                Session::put('idioma', $locale);
                Session::put('idioma_id', $idioma->id);
            } else {
                // Si no se encuentra, usar el idioma por defecto
                $idiomaDefecto = Idioma::where('principal', true)
                                       ->where('activado', true)
                                       ->first();
                
                if ($idiomaDefecto) {
                    App::setLocale($idiomaDefecto->codigo);
                    Session::put('idioma', $idiomaDefecto->codigo);
                    Session::put('idioma_id', $idiomaDefecto->id);
                } else {
                    // Fallback si no hay idiomas configurados
                    App::setLocale('es');
                    Session::put('idioma', 'es');
                    Session::put('idioma_id', 1);
                }
            }
        } catch (\Exception $e) {
            // En caso de error, usar configuración por defecto
            \Log::error('Error en SetLocale middleware: ' . $e->getMessage());
            App::setLocale('es');
            Session::put('idioma', 'es');
            Session::put('idioma_id', 1);
        }
        
        return $next($request);
    }
}
