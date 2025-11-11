<?php

use App\Models\Menu;
use App\Models\TextoIdioma;
use App\Models\Idioma;
use App\Models\TipoContenido;

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

echo "🚀 Creando menús de ejemplo...\n\n";

// Obtener idiomas activos
$idiomas = Idioma::where('activo', true)->get();
$idiomaPrincipal = Idioma::where('es_principal', true)->first();

echo "📋 Idiomas disponibles:\n";
foreach ($idiomas as $idioma) {
    echo "   - {$idioma->nombre} ({$idioma->etiqueta})" . ($idioma->es_principal ? " [PRINCIPAL]" : "") . "\n";
}
echo "\n";

// Crear menú principal de ejemplo
echo "🏠 Creando menú 'Inicio'...\n";
$menuInicio = Menu::create([
    'parent_id' => null,
    'tipo_enlace' => 'ninguno',
    'tipo_contenido_id' => null,
    'content_id' => null,
    'url' => null,
    'icon' => 'fas fa-home',
    'visible' => true,
    'abrir_nueva_ventana' => false,
    'menu_pie' => true,
    'orden' => 1,
]);

// Crear textos para el menú en todos los idiomas
foreach ($idiomas as $idioma) {
    $titulo = $idioma->etiqueta === 'es' ? 'Inicio' : 'Home';
    
    TextoIdioma::create([
        'objeto_type' => 'App\Models\Menu',
        'objeto_id' => $menuInicio->id,
        'idioma_id' => $idioma->id,
        'campo' => 'titulo',
        'texto' => $titulo,
        'slug' => Str::slug($titulo),
        'activo' => true,
    ]);
    
    echo "   ✅ Texto creado para {$idioma->nombre}: {$titulo}\n";
}

echo "\n🎭 Creando menú 'Nosotros'...\n";
$menuNosotros = Menu::create([
    'parent_id' => null,
    'tipo_enlace' => 'url_externa',
    'tipo_contenido_id' => null,
    'content_id' => null,
    'url' => 'https://ejemplo.com/nosotros',
    'icon' => 'fas fa-users',
    'visible' => true,
    'abrir_nueva_ventana' => true,
    'menu_pie' => false,
    'orden' => 2,
]);

// Crear textos para el segundo menú
foreach ($idiomas as $idioma) {
    $titulo = $idioma->etiqueta === 'es' ? 'Nosotros' : 'About Us';
    
    TextoIdioma::create([
        'objeto_type' => 'App\Models\Menu',
        'objeto_id' => $menuNosotros->id,
        'idioma_id' => $idioma->id,
        'campo' => 'titulo',
        'texto' => $titulo,
        'slug' => Str::slug($titulo),
        'activo' => true,
    ]);
    
    echo "   ✅ Texto creado para {$idioma->nombre}: {$titulo}\n";
}

// Crear un submenú
echo "\n📂 Creando submenú 'Historia'...\n";
$menuHistoria = Menu::create([
    'parent_id' => $menuNosotros->id,
    'tipo_enlace' => 'ninguno',
    'tipo_contenido_id' => null,
    'content_id' => null,
    'url' => null,
    'icon' => 'fas fa-history',
    'visible' => true,
    'abrir_nueva_ventana' => false,
    'menu_pie' => false,
    'orden' => 1,
]);

foreach ($idiomas as $idioma) {
    $titulo = $idioma->etiqueta === 'es' ? 'Historia' : 'History';
    
    TextoIdioma::create([
        'objeto_type' => 'App\Models\Menu',
        'objeto_id' => $menuHistoria->id,
        'idioma_id' => $idioma->id,
        'campo' => 'titulo',
        'texto' => $titulo,
        'slug' => Str::slug($titulo),
        'activo' => true,
    ]);
    
    echo "   ✅ Texto creado para {$idioma->nombre}: {$titulo}\n";
}

echo "\n🎉 ¡Menús de ejemplo creados exitosamente!\n\n";

// Verificar la creación
echo "📊 Verificando menús creados:\n";
$menus = Menu::with(['textos.idioma', 'children'])->whereNull('parent_id')->orderBy('orden')->get();

foreach ($menus as $menu) {
    echo "\n🔹 {$menu->titulo} (ID: {$menu->id})\n";
    echo "   - Tipo: {$menu->tipo_enlace}\n";
    echo "   - Visible: " . ($menu->visible ? 'SÍ' : 'NO') . "\n";
    echo "   - Pie: " . ($menu->menu_pie ? 'SÍ' : 'NO') . "\n";
    echo "   - Submenús: {$menu->children->count()}\n";
    
    if ($menu->children->count() > 0) {
        foreach ($menu->children as $submenu) {
            echo "     └─ {$submenu->titulo}\n";
        }
    }
}

echo "\n✅ ¡Todo listo! Puedes ver los menús en: http://127.0.0.1:8002/admin/menus\n";