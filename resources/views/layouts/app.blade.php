<!DOCTYPE html>
<html lang="{{ idioma_etiqueta_html() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $configuracion->metatitulo ?? 'Nuntris Teatro')</title>
    <meta name="description" content="@yield('meta_description', $configuracion->metadescripcion ?? 'Compañía de teatro asturiana')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKB4Imkb9hXg9lf+Z8+C3p9z9W/TCLpK1J2vjVvZ+8abtTE1Pi6jizo" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --brand-primary: #13213f;
            --brand-secondary: #f9a826;
            --brand-light: #f5f7fb;
        }

        body {
            font-family: 'Roboto', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--brand-light);
            color: #1a1d2d;
        }

        .site-header {
            background: linear-gradient(135deg, rgba(16, 23, 38, 0.95), rgba(25, 36, 60, 0.95));
            color: #fff;
        }

        .site-header .navbar-brand {
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .site-header .navbar-brand span {
            font-weight: 400;
            text-transform: none;
            letter-spacing: normal;
        }

        .site-header .nav-link {
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .site-header .dropdown-menu {
            border-radius: 0.75rem;
            border: none;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        .hero-section {
            position: relative;
            overflow: hidden;
        }

        .hero-slide {
            min-height: clamp(22rem, 60vh, 36rem);
            background-position: center;
            background-size: cover;
            display: flex;
            align-items: center;
            color: #fff;
        }

        .hero-overlay {
            background: linear-gradient(135deg, rgba(10, 16, 28, 0.85), rgba(19, 33, 63, 0.65));
            width: 100%;
            padding: clamp(3rem, 5vw, 5rem) 0;
        }

        .hero-caption h1,
        .hero-caption h2 {
            font-weight: 700;
        }

        main {
            flex-grow: 1;
        }

        .footer {
            background: #101726;
            color: #d9deeb;
        }

        .footer a {
            color: inherit;
        }

        .footer a:hover {
            color: var(--brand-secondary);
        }

        .footer__divider {
            border-color: rgba(255, 255, 255, 0.08);
        }

        .btn-language {
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .dropdown-menu .active {
            background-color: rgba(19, 33, 63, 0.08);
            color: var(--brand-primary);
        }

        @media (max-width: 991px) {
            .site-header .navbar-collapse {
                padding: 1rem 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    @php
        $idiomaActual = function_exists('idioma_actual') ? idioma_actual() : app()->getLocale();
        $idiomaRuta = etiqueta_para_ruta_idioma($idiomaActual ?? 'es');
        $idiomasDisponibles = function_exists('idiomas_disponibles') ? idiomas_disponibles() : [];
        $menusCollection = isset($menus) ? collect($menus) : collect();
    @endphp

    <header class="site-header shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-dark py-3">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('inicio', ['idioma' => $idiomaRuta]) }}">
                    <span>{{ $configuracion->nombre_empresa ?? 'Nuntris Teatro' }}</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primaryNav" aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

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

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('noticias', ['idioma' => $idiomaRuta]) }}">{{ __('Noticias') }}</a>
                        </li>

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

    <footer class="footer mt-auto pt-5 pb-4">
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-6 col-lg-4">
                    <h5 class="text-uppercase fw-bold mb-3">{{ $configuracion->nombre_empresa ?? 'Nuntris Teatro' }}</h5>
                    <p class="mb-0">{{ $configuracion->metadescripcion ?? 'Compañía de teatro asturiana especializada en obras clásicas y contemporáneas.' }}</p>
                </div>
                    <div class="col-lg-4 mb-3 mb-lg-0 d-flex align-items-center">
                        <a href="/" class="d-inline-block align-middle me-3">
                            <img src="/storage/logo-nuntristeatro.png" alt="Nun Tris Teatro" height="48">
                        </a>
                        <span class="align-middle small">&copy; {{ date('Y') }} Nun Tris Teatro</span>
                    </div>
                    <div class="col-lg-4 mb-3 mb-lg-0">
                        <nav class="nav justify-content-center">
                            @foreach($menuPrincipal as $item)
                                @if($item->mostrar_en_footer)
                                    <a class="nav-link px-2 text-light" href="{{ menu_url($item, $idiomaRuta) }}">{{ $item->nombre }}</a>
                                @endif
                            @endforeach
                        </nav>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="https://www.facebook.com/nuntristeatro" class="text-light me-3" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="https://www.instagram.com/nuntristeatro" class="text-light me-3" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="mailto:info@nuntristeatro.com" class="text-light" aria-label="Email"><i class="fa fa-envelope fa-lg"></i></a>
                    </div>
            </div>
            <hr class="footer__divider my-4">
            <div class="text-center small">
                &copy; {{ now()->year }} {{ $configuracion->nombre_empresa ?? 'Nuntris Teatro' }} · {{ __('Todos los derechos reservados') }}
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>