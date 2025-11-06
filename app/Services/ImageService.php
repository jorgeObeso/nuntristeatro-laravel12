<?php

namespace App\Services;

use App\Models\ImageConfig;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class ImageService
{
    protected $disk = 'public';
    protected $imageManager;

    public function __construct()
    {
        // Verificar si GD está disponible antes de inicializar
        if (extension_loaded('gd')) {
            $this->imageManager = new ImageManager(new Driver());
        } else {
            $this->imageManager = null;
            \Log::warning('Extensión GD no disponible. Las imágenes se guardarán sin procesamiento.');
        }
    }

    /**
     * Procesar y guardar imagen según configuración
     */
    public function processAndSaveImage(UploadedFile $file, string $tipoContenido, string $tipoImagen, int $contentId = null): ?string
    {
        try {
            // Obtener configuración para este tipo de contenido e imagen
            $config = ImageConfig::getConfig($tipoContenido, $tipoImagen);
            
            if (!$config) {
                // Configuración por defecto si no existe
                $config = (object)[
                    'ancho' => 800,
                    'alto' => 600,
                    'mantener_aspecto' => true,
                    'formato' => 'jpg',
                    'calidad' => 85,
                    'redimensionar' => true
                ];
            }

            // Crear directorio si no existe
            $directory = "images/{$tipoContenido}/{$tipoImagen}";
            Storage::disk($this->disk)->makeDirectory($directory);
            
            // Si GD no está disponible, simplemente guardar el archivo original
            if (!$this->imageManager) {
                $originalExtension = $file->getClientOriginalExtension();
                $filename = $this->generateFilename($file, $originalExtension, $contentId);
                $path = "{$directory}/{$filename}";
                
                Storage::disk($this->disk)->putFileAs($directory, $file, $filename);
                
                \Log::info("Imagen guardada sin procesamiento (GD no disponible): {$path}");
                return $path;
            }

            // Generar nombre único para el archivo
            $filename = $this->generateFilename($file, $config->formato, $contentId);
            
            // Procesar imagen según configuración
            $image = $this->imageManager->read($file->getRealPath());
            
            if ($config->redimensionar) {
                if ($config->mantener_aspecto) {
                    // Redimensionar manteniendo proporción (fit dentro de las dimensiones)
                    $image->scaleDown(width: $config->ancho, height: $config->alto);
                } else {
                    // Redimensionar forzando tamaño exacto
                    $image->resize($config->ancho, $config->alto);
                }
            }

            // Aplicar compresión según formato
            switch ($config->formato) {
                case 'jpg':
                case 'jpeg':
                    $encodedImage = $image->toJpeg($config->calidad);
                    break;
                case 'png':
                    $encodedImage = $image->toPng();
                    break;
                case 'webp':
                    $encodedImage = $image->toWebp($config->calidad);
                    break;
                default:
                    $encodedImage = $image->toJpeg($config->calidad);
            }

            // Guardar imagen procesada
            $path = "{$directory}/{$filename}";
            Storage::disk($this->disk)->put($path, $encodedImage);

            return $path;

        } catch (\Exception $e) {
            \Log::error('Error procesando imagen: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generar nombre único para archivo
     */
    protected function generateFilename(UploadedFile $file, string $formato, int $contentId = null): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($originalName);
        $timestamp = time();
        $random = Str::random(6);
        
        if ($contentId) {
            return "content_{$contentId}_{$safeName}_{$timestamp}_{$random}.{$formato}";
        }
        
        return "{$safeName}_{$timestamp}_{$random}.{$formato}";
    }

    /**
     * Eliminar imagen del storage
     */
    public function deleteImage(string $imagePath): bool
    {
        try {
            if (Storage::disk($this->disk)->exists($imagePath)) {
                return Storage::disk($this->disk)->delete($imagePath);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Error eliminando imagen: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener URL pública de la imagen
     */
    public function getImageUrl(string $imagePath): string
    {
        return Storage::disk($this->disk)->url($imagePath);
    }

    /**
     * Validar archivo de imagen
     */
    public function validateImageFile(UploadedFile $file): array
    {
        $errors = [];
        
        // Validar tamaño (máximo 10MB)
        if ($file->getSize() > 10 * 1024 * 1024) {
            $errors[] = 'El archivo es demasiado grande. Máximo 10MB.';
        }
        
        // Validar tipo MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Formato de imagen no válido. Use JPG, PNG, GIF o WebP.';
        }
        
        // Validar dimensiones mínimas
        $imageInfo = getimagesize($file->getRealPath());
        if ($imageInfo) {
            if ($imageInfo[0] < 100 || $imageInfo[1] < 100) {
                $errors[] = 'La imagen es demasiado pequeña. Mínimo 100x100 píxeles.';
            }
        }
        
        return $errors;
    }

    /**
     * Obtener configuraciones de imagen para un tipo de contenido
     */
    public function getImageConfigs(string $tipoContenido): array
    {
        return ImageConfig::where('tipo_contenido', $tipoContenido)
                         ->where('activo', true)
                         ->get()
                         ->keyBy('tipo_imagen')
                         ->toArray();
    }
}