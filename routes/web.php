<?php

use App\Http\Controllers\WebController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ContentAdminController;
use App\Http\Controllers\Admin\MenuAdminController;
use App\Http\Controllers\Admin\ImageConfigController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\IdiomaController;
use Illuminate\Support\Facades\Route;

// Ruta principal - redirección al idioma por defecto
Route::get('/', [WebController::class, 'index'])->name('principal');

// Cambiar idioma
Route::get('/idioma/{idioma}', [WebController::class, 'cambiarIdioma'])->name('cambiar-idioma');

// Rutas con idioma
Route::middleware(['locale'])->group(function () {
    // Página de inicio
    Route::get('/{idioma}', [WebController::class, 'inicio'])
        ->where('idioma', '^(es|as)$')
        ->name('inicio');
    
    // Noticias
    Route::get('/{idioma}/noticias', [WebController::class, 'noticias'])
        ->where('idioma', '^(es|as)$')
        ->name('noticias');
    
    // Galerías
    Route::get('/{idioma}/galerias', [GalleryController::class, 'index'])
        ->where('idioma', '^(es|as)$')
        ->name('galleries.index');
    
    Route::get('/{idioma}/galerias/{slug}', [GalleryController::class, 'show'])
        ->where(['idioma' => '^(es|as)$', 'slug' => '[a-zA-Z0-9\-_]+'])
        ->name('galleries.show');
    
    // Contenido dinámico
    Route::get('/{idioma}/{slug}', [WebController::class, 'contenido'])
        ->where(['idioma' => '^(es|as)$', 'slug' => '[a-zA-Z0-9\-_]+'])
        ->name('contenido');
});

// Ruta global de login (requerida por Laravel)
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Rutas de administración CMS Eunomia
Route::prefix('admin')->name('admin.')->group(function () {
    // Ruta temporal para crear admin (remover en producción)
    Route::get('/setup', [AdminController::class, 'createTestAdmin'])->name('setup');
    
    // Login
    Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    
    // Dashboard y gestión (requieren autenticación)
    Route::middleware(['auth'])->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard.explicit');
        
        // Gestión de contenidos
        Route::get('contents', [ContentAdminController::class, 'index'])->name('contents.index');
        Route::get('contents/create', [ContentAdminController::class, 'create'])->name('contents.create');
        Route::post('contents', [ContentAdminController::class, 'store'])->name('contents.store');
        Route::get('contents/{content}', [ContentAdminController::class, 'show'])->name('contents.show');
        Route::get('contents/{content}/edit', [ContentAdminController::class, 'edit'])->name('contents.edit');
        Route::put('contents/{content}', [ContentAdminController::class, 'update'])->name('contents.update');
        Route::delete('contents/{content}', [ContentAdminController::class, 'destroy'])->name('contents.destroy');
        
        // Gestión de menús
        Route::resource('menus', MenuAdminController::class);
        Route::post('menus/update-order', [MenuAdminController::class, 'updateOrder'])->name('menus.update-order');
        
        // Configuración de imágenes
        Route::resource('image-configs', ImageConfigController::class);
        
        // Gestión de galerías
        Route::resource('galleries', AdminGalleryController::class);
        Route::post('galleries/{gallery}/upload-images', [AdminGalleryController::class, 'uploadImages'])->name('galleries.upload-images');
        Route::post('galleries/{gallery}/update-order', [AdminGalleryController::class, 'updateImageOrder'])->name('galleries.update-order');
        Route::delete('galleries/{gallery}/images/{image}', [AdminGalleryController::class, 'deleteImage'])->name('galleries.delete-image');
        
        // Gestión de textos multiidioma para imágenes de galería
        Route::get('gallery-images/{image}/texts', [AdminGalleryController::class, 'getImageTexts'])->name('gallery-images.texts.get');
        Route::post('gallery-images/{image}/texts', [AdminGalleryController::class, 'saveImageTexts'])->name('gallery-images.texts.save');
        
        // Gestión de idiomas
        Route::resource('idiomas', IdiomaController::class);
        Route::post('idiomas/update-order', [IdiomaController::class, 'updateOrder'])->name('idiomas.update-order');
        Route::post('idiomas/{idioma}/toggle-active', [IdiomaController::class, 'toggleActive'])->name('idiomas.toggle-active');
        
        // Upload de imágenes para TinyMCE
        Route::post('upload-image', [ContentAdminController::class, 'uploadImage'])->name('upload-image');
    });
});
