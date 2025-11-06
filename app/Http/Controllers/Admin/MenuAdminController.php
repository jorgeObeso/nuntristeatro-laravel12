<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\TextoIdioma;
use App\Models\Idioma;
use App\Models\TipoContenido;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::with(['textos.idioma'])
                    ->orderBy('orden')
                    ->get();
                    
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $idiomas = Idioma::where('activado', true)->get();
        $menusParent = Menu::whereNull('parent_id')->get();
        
        return view('admin.menus.create', compact('idiomas', 'menusParent'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'orden' => 'required|integer',
            'visible' => 'boolean',
            'blank' => 'boolean',
            'parent_id' => 'nullable|exists:menus,id',
            'textos' => 'required|array',
            'textos.*.titulo' => 'required|string|max:255',
        ]);

        // Crear el menú principal
        $menu = Menu::create([
            'enlace' => $request->enlace,
            'orden' => $request->orden,
            'visible' => $request->visible ?? true,
            'blank' => $request->blank ?? false,
            'parent_id' => $request->parent_id,
        ]);

        // Crear textos en diferentes idiomas
        foreach ($request->textos as $idiomaId => $textoData) {
            if (!empty($textoData['titulo'])) {
                $tipoContenido = TipoContenido::where('tipo_contenido', 'Menu')->first();
                
                TextoIdioma::create([
                    'idioma_id' => $idiomaId,
                    'menu_id' => $menu->id,
                    'tipo_contenido_id' => $tipoContenido->id,
                    'titulo' => $textoData['titulo'],
                    'slug' => Str::slug($textoData['titulo']),
                    'visible' => true,
                ]);
            }
        }

        return redirect()->route('admin.menus.index')
                        ->with('success', 'Menú creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        $menu->load(['textos.idioma', 'parent', 'children']);
        return view('admin.menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $menu->load(['textos.idioma']);
        $idiomas = Idioma::where('activado', true)->get();
        $menusParent = Menu::where('id', '!=', $menu->id)->whereNull('parent_id')->get();
        
        return view('admin.menus.edit', compact('menu', 'idiomas', 'menusParent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'orden' => 'required|integer',
            'visible' => 'boolean',
            'blank' => 'boolean',
            'parent_id' => 'nullable|exists:menus,id',
            'textos' => 'required|array',
            'textos.*.titulo' => 'required|string|max:255',
        ]);

        // Actualizar el menú principal
        $menu->update([
            'enlace' => $request->enlace,
            'orden' => $request->orden,
            'visible' => $request->visible ?? true,
            'blank' => $request->blank ?? false,
            'parent_id' => $request->parent_id,
        ]);

        // Actualizar o crear textos
        foreach ($request->textos as $idiomaId => $textoData) {
            if (!empty($textoData['titulo'])) {
                $texto = TextoIdioma::where('menu_id', $menu->id)
                                   ->where('idioma_id', $idiomaId)
                                   ->first();

                $data = [
                    'titulo' => $textoData['titulo'],
                    'slug' => Str::slug($textoData['titulo']),
                    'visible' => true,
                ];

                if ($texto) {
                    $texto->update($data);
                } else {
                    $tipoContenido = TipoContenido::where('tipo_contenido', 'Menu')->first();
                    TextoIdioma::create(array_merge($data, [
                        'idioma_id' => $idiomaId,
                        'menu_id' => $menu->id,
                        'tipo_contenido_id' => $tipoContenido->id,
                    ]));
                }
            }
        }

        return redirect()->route('admin.menus.index')
                        ->with('success', 'Menú actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        // Eliminar textos relacionados
        $menu->textos()->delete();
        
        // Eliminar submenús si los hay
        $menu->children()->each(function($child) {
            $child->textos()->delete();
            $child->delete();
        });
        
        // Eliminar el menú
        $menu->delete();

        return redirect()->route('admin.menus.index')
                        ->with('success', 'Menú eliminado exitosamente.');
    }

    /**
     * Actualizar el orden de los menús
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'menus' => 'required|array',
            'menus.*.id' => 'required|exists:menus,id',
            'menus.*.orden' => 'required|integer',
        ]);

        foreach ($request->menus as $menuData) {
            Menu::where('id', $menuData['id'])
                ->update(['orden' => $menuData['orden']]);
        }

        return response()->json(['success' => true]);
    }
}
