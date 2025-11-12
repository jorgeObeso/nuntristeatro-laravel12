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
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Ruta principal - redirección al idioma por defecto
Route::get('/', [WebController::class, 'index'])->name('principal');

// API temporal para verificar datos (sin autenticación)
Route::get('api/matrix-data', function() {
    $roles = App\Models\Role::with('permissions')->orderBy('nombre')->get();
    $permissions = App\Models\Permission::where('activo', true)->orderBy('modulo')->orderBy('tipo_permiso')->get();
    
    $matrix = [];
    foreach ($roles as $role) {
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $matrix[$role->id] = $rolePermissions;
    }
    
    return response()->json([
        'roles' => $roles->map(function($role) {
            return [
                'id' => $role->id,
                'nombre' => $role->nombre,
                'permissions_count' => $role->permissions->count(),
                'permissions' => $role->permissions->pluck('id')->toArray()
            ];
        }),
        'permissions' => $permissions->map(function($permission) {
            return [
                'id' => $permission->id,
                'nombre' => $permission->nombre,
                'modulo' => $permission->modulo,
                'activo' => $permission->activo
            ];
        }),
        'matrix' => $matrix,
        'raw_relations' => DB::table('role_permissions')
            ->join('roles', 'roles.id', '=', 'role_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->select('roles.nombre as role_name', 'roles.id as role_id', 'permissions.nombre as perm_name', 'permissions.id as perm_id')
            ->get()
    ]);
});

// Matrix temporal sin autenticación
Route::get('test-matrix', function() {
    return view('admin.clean-matrix');
});

// Matrix con JavaScript completo
Route::get('matrix-test', function() {
    return view('admin.matrix-test');
});

// Debug de la página original
Route::get('matrix-original', function() {
    $roles = App\Models\Role::with('permissions')->orderBy('nombre')->get();
    $permissions = App\Models\Permission::where('activo', true)
        ->orderBy('modulo')
        ->orderBy('tipo_permiso')
        ->get();
    
    $permissionsByModule = $permissions->groupBy('modulo');
    
    // Crear matriz de roles x permisos
    $matrix = [];
    foreach ($roles as $role) {
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $matrix[$role->id] = $rolePermissions;
    }
    
    return view('admin.roles.permission-matrix', compact('roles', 'permissions', 'permissionsByModule', 'matrix'));
});

// Debug detallado de event listeners
Route::get('matrix-debug', function() {
    return view('admin.matrix-debug');
});

// Versión limpia sin AdminLTE
Route::get('matrix-clean', function() {
    $roles = App\Models\Role::with('permissions')->orderBy('nombre')->get();
    $permissions = App\Models\Permission::where('activo', true)
        ->orderBy('modulo')
        ->orderBy('tipo_permiso')
        ->get();
    
    $permissionsByModule = $permissions->groupBy('modulo');
    
    // Crear matriz de roles x permisos
    $matrix = [];
    foreach ($roles as $role) {
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $matrix[$role->id] = $rolePermissions;
    }
    
    return view('admin.matrix-clean', compact('roles', 'permissions', 'permissionsByModule', 'matrix'));
});

// Guardado temporal sin autenticación
Route::post('test-matrix-save', function(Illuminate\Http\Request $request) {
    try {
        $matrix = $request->json('matrix') ?? $request->input('matrix');
        
        if (!$matrix || !is_array($matrix)) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de matriz inválidos.'
            ], 400);
        }

        \DB::transaction(function() use ($matrix) {
            foreach ($matrix as $roleId => $permissionIds) {
                $role = App\Models\Role::findOrFail($roleId);
                $role->permissions()->sync($permissionIds);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Matriz de permisos actualizada correctamente (TEST MODE).'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Guardado temporal usando GET para evitar CSRF
Route::get('test-save-permissions/{roleId}/{permissionIds}', function($roleId, $permissionIds) {
    try {
        $permissionArray = explode(',', $permissionIds);
        $permissionArray = array_filter(array_map('intval', $permissionArray));
        
        $role = App\Models\Role::findOrFail($roleId);
        $role->permissions()->sync($permissionArray);
        
        return response()->json([
            'success' => true,
            'message' => "Rol {$role->nombre} actualizado con permisos: " . implode(',', $permissionArray)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

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
        Route::post('idiomas/update-order', [IdiomaController::class, 'updateOrder'])->name('idiomas.update-order');
    Route::post('idiomas/{idioma}/toggle-active', [IdiomaController::class, 'toggleActive'])->name('idiomas.toggle-active');
        
        // Contenido
        Route::resource('contents', ContentAdminController::class);
        
        // Tipos de contenido
        Route::resource('tipos-contenido', \App\Http\Controllers\Admin\TipoContenidoController::class);
        
        // Configuración de imágenes
        Route::resource('image-configs', ImageConfigController::class);
        
        // Galerías
    Route::resource('galleries', AdminGalleryController::class);
    Route::post('galleries/{gallery}/images', [AdminGalleryController::class, 'uploadImages'])->name('galleries.images.upload');
    Route::post('galleries/{gallery}/update-order', [AdminGalleryController::class, 'updateImageOrder'])->name('galleries.images.update-order');
    Route::delete('galleries/{gallery}/images/{image}', [AdminGalleryController::class, 'deleteImage'])->name('galleries.images.delete');
    Route::get('gallery-images/{image}/texts', [AdminGalleryController::class, 'getImageTexts'])->name('gallery-images.texts.show');
    Route::post('gallery-images/{image}/texts', [AdminGalleryController::class, 'saveImageTexts'])->name('gallery-images.texts.save');
        
        // Menús
        Route::resource('menus', MenuAdminController::class);
        Route::get('menus-contents-by-type', [MenuAdminController::class, 'getContentsByType'])->name('menus.get-contents-by-type');
        Route::get('menus-test-ajax', [MenuAdminController::class, 'testAjax'])->name('menus.test-ajax');
        Route::post('menus/update-order', [MenuAdminController::class, 'updateOrder'])->name('menus.update-order');
        
        // Slides
        Route::resource('slides', SlideAdminController::class);
        Route::post('slides/update-order', [SlideAdminController::class, 'updateOrder'])->name('slides.update-order');
        
        // Gestión de usuarios, roles y permisos
        Route::resource('roles', RoleController::class);
        Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
        Route::get('roles/permission-matrix/view', [RoleController::class, 'permissionMatrix'])->name('roles.permission-matrix');
        Route::post('roles/permission-matrix/update', [RoleController::class, 'updatePermissionMatrix'])->name('roles.permission-matrix.update');
        
        // Ruta de debug temporal
        Route::get('debug-session', function() {
            return view('admin.debug-session');
        })->name('debug-session');
        
        // Matrix simple para testing
        Route::get('simple-matrix', function() {
            return view('admin.simple-matrix');
        })->name('simple-matrix');
        
        // Matrix limpia sin CSS ni JS complejo
        Route::get('clean-matrix', function() {
            return view('admin.clean-matrix');
        })->name('clean-matrix');
        
        Route::resource('permissions', PermissionController::class);
        
        Route::resource('users', UserController::class);
    });
});
