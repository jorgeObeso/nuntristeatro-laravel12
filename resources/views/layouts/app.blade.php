<!DOCTYPE html>
<html lang="{{ idioma_etiqueta_html() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $idiomaEtiqueta = app()->getLocale() ?? (function_exists('idioma_actual') ? idioma_actual() : 'es');
        $textoMeta = $configEmpresa?->textos?->where('idioma.etiqueta', $idiomaEtiqueta)->first();
        if (!$textoMeta) {
            // Fallback al idioma principal
            $textoMeta = $configEmpresa?->textos?->where('idioma.es_principal', true)->first();
        }
        $metaTitulo = $textoMeta?->metatitulo ?? 'Nuntris Teatro';
        $metaDescripcion = $textoMeta?->metadescripcion ?? 'Compañía de teatro asturiana';
    @endphp
    <title>@yield('metaTitulo', $metaTitulo)</title>
    <meta name="description" content="@yield('meta_description', $metaDescripcion)">

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />


    @vite(['resources/css/web.css'])
    @stack('styles')
</head>
<body>
    @php
        $idiomaActual = function_exists('idioma_actual') ? idioma_actual() : app()->getLocale();
        $idiomaRuta = \App\Helpers\IdiomaHelper::etiquetaParaRuta($idiomaActual ?? 'es');
        $idiomasDisponibles = function_exists('idiomas_disponibles') ? idiomas_disponibles() : [];
        $menusCollection = isset($menus) ? collect($menus) : collect();
    @endphp


    <header>
        <nav class="navbar navbar-expand-lg navbar-dark py-3">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('inicio', ['idioma' => $idiomaRuta]) }}">
                    <span><img src="/storage/logo.png" alt="Logo Nun Tris Teatro"></span>     
                </a>

     

                <div class="collapse navbar-collapse" id="primaryNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('inicio', ['idioma' => $idiomaRuta]) }}">{{ __('Inicio') }}</a>
                        </li>

                        @foreach($menusCollection as $menu)
                            @php
                                $children = ($menu->children ?? collect())->filter(fn($child) => (bool) $child->visible);
                                $hasChildren = $children->isNotEmpty();
                                $menuTitle = trim($menu->titulo ?? '');
                                $menuLink = menu_url($menu, $idiomaActual);
                                $menuTarget = $menu->abrir_nueva_ventana ? '_blank' : '_self';
                            @endphp

                            @if($menuTitle !== '')
                                <li class="nav-item {{ $hasChildren ? 'dropdown' : '' }}">
                                    @if($hasChildren)
                                        <a class="nav-link dropdown-toggle" href="{{ $menuLink }}" role="button" data-bs-toggle="dropdown" aria-expanded="false" target="{{ $menuTarget }}">
                                            {{ $menuTitle }}
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
                                            @foreach($children as $child)
                                                @php
                                                    $childTitle = trim($child->titulo ?? '');
                                                    $childLink = menu_url($child, $idiomaActual);
                                                    $childTarget = $child->abrir_nueva_ventana ? '_blank' : '_self';
                                                @endphp
                                                @if($childTitle !== '')
                                                    <li><a class="dropdown-item" href="{{ $childLink }}" target="{{ $childTarget }}">{{ $childTitle }}</a></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @else
                                        <a class="nav-link" href="{{ $menuLink }}" target="{{ $menuTarget }}">{{ $menuTitle }}</a>
                                    @endif
                                </li>
                            @endif
                        @endforeach



                        <li class="nav-item mt-3 mt-lg-0">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-light btn-language dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ strtoupper($idiomaRuta) }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                                    @foreach($idiomasDisponibles as $idioma)
                                        <li>
                                            <a class="dropdown-item {{ es_idioma_actual($idioma['etiqueta']) ? 'active fw-semibold' : '' }}" href="{{ $idioma['url'] }}">
                                                {{ $idioma['nombre'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    @include('web.partials.footer', [
        'configuracion' => $configuracion,
        'configEmpresa' => $configEmpresa ?? null,
        'menuPrincipal' => $menuPrincipal ?? [],
        'idiomaRuta' => $idiomaRuta
    ])

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>