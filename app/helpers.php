<?php

use App\Services\ImageService;

if (!function_exists('responsive_image')) {
    /**
     * Generar URL de imagen responsive
     */
    function responsive_image(string $desktopPath, bool $isMobile = false): string
    {
        $imageService = app(ImageService::class);
        return $imageService->getResponsiveImageUrl($desktopPath, $isMobile);
    }
}

if (!function_exists('responsive_image_html')) {
    /**
     * Generar HTML de imagen responsive con srcset
     */
    function responsive_image_html(string $desktopPath, string $alt = '', string $class = '', string $style = ''): string
    {
        $imageService = app(ImageService::class);
        return $imageService->generateResponsiveImageHtml($desktopPath, $alt, $class, $style);
    }
}

if (!function_exists('get_image_alt')) {
    /**
     * Obtener descripción ALT apropiada para el idioma actual
     */
    function get_image_alt($content, $texto = null, string $tipo = 'imagen'): string
    {
        // Si hay texto específico del idioma, usar su ALT
        if ($texto && $tipo === 'imagen' && $texto->imagen_alt) {
            return $texto->imagen_alt;
        }
        if ($texto && $tipo === 'imagen_portada' && $texto->imagen_portada_alt) {
            return $texto->imagen_portada_alt;
        }
        
        // Fallback al ALT global del contenido
        if ($content) {
            if ($tipo === 'imagen' && $content->imagen_alt) {
                return $content->imagen_alt;
            }
            if ($tipo === 'imagen_portada' && $content->imagen_portada_alt) {
                return $content->imagen_portada_alt;
            }
        }
        
        // Fallback al título del texto o descripción genérica
        if ($texto && $texto->titulo) {
            return $texto->titulo;
        }
        
        return 'Imagen de ' . ($content->tipo_contenido ?? 'contenido');
    }
}

if (!function_exists('get_responsive_image_url')) {
    /**
     * Generar URL de imagen responsive (alias para responsive_image)
     */
    function get_responsive_image_url(string $path, string $type = 'desktop'): string
    {
        $isMobile = ($type === 'mobile');
        return responsive_image($path, $isMobile);
    }
}

// ==================== HELPERS DE IDIOMAS ====================

if (!function_exists('normalizar_etiqueta_idioma')) {
    /**
     * Normaliza etiquetas de idioma procedentes de rutas/BD.
     */
    function normalizar_etiqueta_idioma(?string $etiqueta): ?string
    {
        if ($etiqueta === null) {
            return null;
        }

        return $etiqueta === 'as' ? 'ast' : $etiqueta;
    }
}

if (!function_exists('etiqueta_para_ruta_idioma')) {
    /**
     * Convierte una etiqueta canónica al valor esperado en las rutas.
     */
    function etiqueta_para_ruta_idioma(string $etiqueta): string
    {
        return $etiqueta === 'ast' ? 'as' : $etiqueta;
    }
}

if (!function_exists('idiomas_activos')) {
    /**
     * Obtener todos los idiomas activos del sistema
     */
    function idiomas_activos(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Idioma::activosParaFrontend();
    }
}

if (!function_exists('idioma_principal')) {
    /**
     * Obtener el idioma principal del sistema
     */
    function idioma_principal(): ?\App\Models\Idioma
    {
        return \App\Models\Idioma::principal();
    }
}

if (!function_exists('idioma_actual')) {
    /**
     * Obtener el idioma actual basado en el contexto de la ruta
     */
    function idioma_actual(): string
    {
        // Intentar obtener idioma de la ruta actual
        $idioma = request()->route('idioma');
        
        if ($idioma) {
            return normalizar_etiqueta_idioma($idioma);
        }
        
        // Fallback al idioma principal
        $principal = idioma_principal();
        return $principal ? normalizar_etiqueta_idioma($principal->etiqueta) : 'es';
    }
}

if (!function_exists('url_idioma')) {
    /**
     * Generar URL para un idioma específico manteniendo la ruta actual
     */
    function url_idioma(string $etiquetaIdioma, ?string $ruta = null): string
    {
        $rutaActual = $ruta ?? request()->route()->getName();
        $parametros = request()->route()->parameters();
        
        // Actualizar el parámetro de idioma
        $parametros['idioma'] = etiqueta_para_ruta_idioma($etiquetaIdioma);
        
        try {
            return route($rutaActual, $parametros);
        } catch (\Exception $e) {
            // Fallback a la página principal del idioma
            return route('inicio', ['idioma' => etiqueta_para_ruta_idioma($etiquetaIdioma)]);
        }
    }
}

if (!function_exists('idioma_etiqueta_html')) {
    /**
     * Obtener la etiqueta HTML lang para el idioma actual
     */
    function idioma_etiqueta_html(): string
    {
        $etiqueta = idioma_actual();
        
        // Obtener información completa del idioma si existe en BD
        $idioma = \App\Models\Idioma::where('etiqueta', $etiqueta)->where('activo', true)->first();
        
        return $idioma ? $idioma->codigo_html : strtolower($etiqueta);
    }
}

if (!function_exists('idiomas_disponibles')) {
    /**
     * Obtener array de idiomas para usar en selectores/menús
     */
    function idiomas_disponibles(): array
    {
        return idiomas_activos()->map(function($idioma) {
            return [
                'etiqueta' => $idioma->etiqueta,
                'nombre' => $idioma->nombre,
                'imagen_url' => $idioma->imagen_url,
                'es_principal' => $idioma->es_principal,
                'url' => url_idioma($idioma->etiqueta)
            ];
        })->toArray();
    }
}

if (!function_exists('es_idioma_actual')) {
    /**
     * Verificar si una etiqueta de idioma es el idioma actual
     */
    function es_idioma_actual(string $etiqueta): bool
    {
        return idioma_actual() === normalizar_etiqueta_idioma($etiqueta);
    }
}

if (!function_exists('menu_url')) {
    /**
     * Obtener la URL adecuada para un ítem de menú considerando el idioma actual.
     */
    function menu_url(\App\Models\Menu $menu, ?string $idiomaEtiqueta = null): string
    {
        $menu->loadMissing(['textos.idioma', 'content.textos.idioma']);

        $idiomaEtiqueta = normalizar_etiqueta_idioma($idiomaEtiqueta ?? idioma_actual());
        $idiomaRuta = etiqueta_para_ruta_idioma($idiomaEtiqueta ?? 'es');

        switch ($menu->tipo_enlace) {
            case 'url_externa':
                return $menu->url_externa ?: $menu->url ?: '#';

            case 'ancla':
            case 'anchor':
                return $menu->url ? ('#' . ltrim($menu->url, '#')) : '#';

            case 'contenido':
                if ($menu->content) {
                    $textoMenu = $menu->textos->first(function ($texto) use ($idiomaEtiqueta) {
                        $etiquetaTexto = normalizar_etiqueta_idioma(optional($texto->idioma)->etiqueta);
                        return $texto->activo && $etiquetaTexto === $idiomaEtiqueta;
                    });

                    $slug = $textoMenu?->slug;

                    if (!$slug) {
                        $textoContenido = $menu->content->textos->first(function ($texto) use ($idiomaEtiqueta) {
                            $etiquetaTexto = normalizar_etiqueta_idioma(optional($texto->idioma)->etiqueta);
                            return $texto->activo && $etiquetaTexto === $idiomaEtiqueta;
                        }) ?? $menu->content->textos->first();

                        $slug = $textoContenido?->slug;
                    }

                    if ($slug) {
                        try {
                            return route('contenido', [
                                'idioma' => $idiomaRuta,
                                'slug' => $slug,
                            ]);
                        } catch (\Throwable $e) {
                            // Ignorar excepción y continuar con fallback.
                        }
                    }
                }
                break;
        }

        if (!empty($menu->url)) {
            return $menu->url;
        }

        return '#';
    }
}

if (!function_exists('get_gallery_image_alt')) {
    /**
     * Obtener texto alternativo multiidioma para imágenes de galería
     */
    function get_gallery_image_alt($galleryImage, $idiomaEtiqueta = null): string
    {
        if (!$galleryImage) {
            return 'Imagen de galería';
        }

        // Determinar idioma a usar
        $etiqueta = $idiomaEtiqueta ?: idioma_actual();
        
        // Buscar idioma por etiqueta
        $idioma = \App\Models\Idioma::where('etiqueta', $etiqueta)->first();
        if (!$idioma) {
            $idioma = \App\Models\Idioma::where('es_principal', true)->first();
        }

        if ($idioma && method_exists($galleryImage, 'getMultilingualAltText')) {
            return $galleryImage->getMultilingualAltText($idioma->id);
        }

        // Fallback al método estándar del modelo
        if (method_exists($galleryImage, 'getMultilingualAltText')) {
            return $galleryImage->getMultilingualAltText();
        }

        // Último fallback
        return $galleryImage->alt_text ?: $galleryImage->titulo ?: 'Imagen de galería';
    }
}