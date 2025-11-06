<?php

use App\Http\Controllers\WebController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ContentAdminController;
use App\Http\Controllers\Admin\MenuAdminController;
use App\Http\Controllers\Admin\ImageConfigController;
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
        
        // Upload de imágenes para TinyMCE
        Route::post('upload-image', [ContentAdminController::class, 'uploadImage'])->name('upload-image');
    });
});
