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