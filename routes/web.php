<?php

use App\Http\Controllers\WebController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ContentAdminController;
use App\Http\Controllers\Admin\MenuAdminController;
use App\Http\Controllers\Admin\SlideAdminController;
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

// Ruta para crear admin de prueba (temporal)
Route::get('/create-admin', [AdminController::class, 'createTestAdmin'])->name('create.admin');

// Test endpoint (temporal) - sin autenticación
Route::get('test/menus-contents-by-type', [MenuAdminController::class, 'getContentsByType'])->name('test.menus.get-contents-by-type');

// Test endpoint real - sin autenticación (temporal para testing)
Route::get('test-ajax-real', [MenuAdminController::class, 'getContentsByType'])->name('test.ajax.real');

// Test vista simple
Route::get('test/menu-create', function() {
    return view('admin.menus.create-test');
})->name('test.menu.create');

// Rutas de usuario autenticado
Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        
        // Login y logout específicos del admin
        Route::get('/login', [AdminController::class, 'showLogin'])->withoutMiddleware('auth')->name('login');
        Route::post('/login', [AdminController::class, 'login'])->withoutMiddleware('auth')->name('authenticate');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        
        // Idiomas
        Route::resource('idiomas', IdiomaController::class);
        
        // Contenido
        Route::resource('contents', ContentAdminController::class);
        
        // Configuración de imágenes
        Route::resource('image-configs', ImageConfigController::class);
        
        // Galerías
        Route::resource('galleries', AdminGalleryController::class);
        Route::post('galleries/{gallery}/images', [AdminGalleryController::class, 'storeImage'])->name('galleries.images.store');
        Route::delete('galleries/{gallery}/images/{image}', [AdminGalleryController::class, 'destroyImage'])->name('galleries.images.destroy');
        Route::post('galleries/{gallery}/images/{image}/text', [AdminGalleryController::class, 'storeImageText'])->name('galleries.images.text.store');
        Route::put('galleries/{gallery}/images/{image}/text/{text}', [AdminGalleryController::class, 'updateImageText'])->name('galleries.images.text.update');
        Route::delete('galleries/{gallery}/images/{image}/text/{text}', [AdminGalleryController::class, 'destroyImageText'])->name('galleries.images.text.destroy');
        
        // Menús
        Route::resource('menus', MenuAdminController::class);
        Route::get('menus-contents-by-type', [MenuAdminController::class, 'getContentsByType'])->name('menus.get-contents-by-type');
        Route::get('menus-test-ajax', [MenuAdminController::class, 'testAjax'])->name('menus.test-ajax');
        Route::post('menus/update-order', [MenuAdminController::class, 'updateOrder'])->name('menus.update-order');
        
        // Slides
        Route::resource('slides', SlideAdminController::class);
        Route::post('slides/update-order', [SlideAdminController::class, 'updateOrder'])->name('slides.update-order');
    });
});
